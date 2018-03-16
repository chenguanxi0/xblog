/**
 * @author lufficc
 */
(function ($) {
    var Xblog = {
        init: function () {
            this.bootUp();
        },
        bootUp: function () {
            console.log('bootUp');
            loadComments(false, false);
            initComment();
            initMarkdownTarget();
            initTables();
            autoSize();
            initProjects();
            initDeleteTarget();
            highLightCode();
            imageLiquid();
        },
    };

    function initDeleteTarget() {
        $('.swal-dialog-target').append(function () {
            return "\n" +
                "<form action='" + $(this).attr('data-url') + "' method='post' style='display:none'>\n" +
                "   <input type='hidden' name='_method' value='" + ($(this).data('method') ? $(this).data('method') : 'delete') + "'>\n" +
                "   <input type='hidden' name='_token' value='" + XblogConfig.csrfToken + "'>\n" +
                "</form>\n"
        }).click(function () {
            var deleteForm = $(this).find("form");
            var method = ($(this).data('method') ? $(this).data('method') : 'delete');
            var url = $(this).attr('data-url');
            var data = $(this).data('request-data') ? $(this).data('request-data') : '';
            var title = $(this).data('dialog-title') ? $(this).data('dialog-title') : '删除';
            var message = $(this).data('dialog-msg');
            var type = $(this).data('dialog-type') ? $(this).data('dialog-type') : 'warning';
            var cancel_text = $(this).data('dialog-cancel-text') ? $(this).data('dialog-cancel-text') : '取消';
            var confirm_text = $(this).data('dialog-confirm-text') ? $(this).data('dialog-confirm-text') : '确定';
            var enable_html = $(this).data('dialog-enable-html') == '1';
            var enable_ajax = $(this).data('enable-ajax') == '1';
            console.log(data);
            if (enable_ajax) {
                swal({
                        title: title,
                        text: message,
                        type: type,
                        html: enable_html,
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        cancelButtonText: cancel_text,
                        confirmButtonText: confirm_text,
                        showLoaderOnConfirm: true,
                        closeOnConfirm: true
                    },
                    function () {
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': XblogConfig.csrfToken
                            },
                            url: url,
                            type: method,
                            data: data,
                            success: function (res) {
                                if (res.code == 200) {
                                    swal({
                                        title: 'Succeed',
                                        text: res.msg,
                                        type: "success",
                                        timer: 1000,
                                        confirmButtonText: "OK"
                                    });
                                } else {
                                    swal({
                                        title: 'Failed',
                                        text: "操作失败",
                                        type: "error",
                                        timer: 1000,
                                        confirmButtonText: "OK"
                                    });
                                }
                            },
                            error: function (res) {
                                swal({
                                    title: 'Failed',
                                    text: "操作失败",
                                    type: "error",
                                    timer: 1000,
                                    confirmButtonText: "OK"
                                });
                            }
                        })
                    });
            } else {
                swal({
                        title: title,
                        text: message,
                        type: type,
                        html: enable_html,
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        cancelButtonText: cancel_text,
                        confirmButtonText: confirm_text,
                        closeOnConfirm: true
                    },
                    function () {
                        deleteForm.submit();
                    });
            }
        });
    }

    function loadComments(shouldMoveEnd, force) {
        var container = $('#comments-container');
        if (force || container.children().length <= 0) {
            console.log("loading comments");
            $.ajax({
                method: 'get',
                url: container.data('api-url'),
            }).done(function (data) {
                container.html(data);
                initDeleteTarget();
                highLightCodeOfChild(container);
                if (shouldMoveEnd) {
                    moveEnd($('#comment-submit'));
                }
            });
        }
    }

    function initComment() {
        var form = $('#comment-form');
        var submitBtn = form.find('#comment-submit');
        var commentContent = form.find('#comment-content');

        var username = form.find('input[name=username]');
        var email = form.find('input[name=email]');
        var site = form.find('input[name=site]');

        if (window.localStorage) {
            username.val(localStorage.getItem('comment_username') == undefined ? '' : localStorage.getItem('comment_username'));
            email.val(localStorage.getItem('comment_email') == undefined ? '' : localStorage.getItem('comment_email'));
            site.val(localStorage.getItem('comment_site') == undefined ? '' : localStorage.getItem('comment_site'));
        }

        form.on('submit', function () {
            if (username.length > 0) {
                if ($.trim(username.val()) == '') {
                    username.focus();
                    return false;
                }
                else if ($.trim(email.val()) == '') {
                    email.focus();
                    return false;
                }
            }

            if ($.trim(commentContent.val()) == '') {
                commentContent.focus();
                return false;
            }

            var usernameValue = username.val();
            var emailValue = email.val();
            var siteValue = site.val();

            submitBtn.val('提交中...').addClass('disabled').prop('disabled', true);
            $.ajax({
                method: 'post',
                url: $(this).attr('action'),
                headers: {
                    'X-CSRF-TOKEN': XblogConfig.csrfToken
                },
                data: {
                    commentable_id: form.find('input[name=commentable_id]').val(),
                    commentable_type: form.find('input[name=commentable_type]').val(),
                    content: commentContent.val(),
                    username: usernameValue,
                    email: emailValue,
                    site: siteValue,
                },
            }).done(function (data) {
                if (data.status === 200) {
                    if (window.localStorage) {
                        localStorage.setItem('comment_username', usernameValue);
                        localStorage.setItem('comment_email', emailValue);
                        localStorage.setItem('comment_site', siteValue);
                    }
                    username.val('');
                    email.val('');
                    site.val('');
                    commentContent.val('');
                    form.find('#comment_error_msg').text('');
                    loadComments(true, true);
                } else {
                    form.find('#comment_error_msg').text(data.msg);
                }
            }).always(function () {
                submitBtn.val("回复").removeClass('disabled').prop('disabled', false);
            });
            return false;
        });
    }

    function initMarkdownTarget() {
        $('.markdown-target').each(function (i, element) {
            element.innerHTML =
                marked($(element).data("markdown"), {
                    renderer: new marked.Renderer(),
                    gfm: true,
                    tables: true,
                    breaks: false,
                    pedantic: false,
                    smartLists: true,
                    smartypants: false,
                });
        });
    }

    function highLightCode() {
        $('pre code').each(function (i, block) {
            hljs.highlightBlock(block);
        });
    }

    function highLightCodeOfChild(parent) {
        $('pre code', parent).each(function (i, block) {
            console.log(block);
            hljs.highlightBlock(block);
        });
    }

    function initTables() {
        $('table').addClass('table table-bordered table-responsive');
    }

    function autoSize() {
        autosize($('.autosize-target'));
    }

    function imageLiquid() {
        $(".js-imgLiquid").imgLiquid({
            fill: true,
            horizontalAlign: "center",
            verticalAlign: "top"
        });
    }

    function initProjects() {
        var projects = $('.projects');
        if (projects.length > 0) {
            $.get('https://api.github.com/users/' + XblogConfig.github_username + '/repos?type=owner',
                function (repositories) {
                    if (!repositories) {
                        projects.html('<div><h3>加载失败</h3><p>请刷新或稍后再试...</p></div>');
                        return;
                    }
                    projects.html('');
                    repositories = repositories.sort(function (repo1, repo2) {
                        return repo2.stargazers_count - repo1.stargazers_count;
                    });
                    repositories = repositories.filter(function (repo) {
                        return repo.description != null;
                    });
                    repositories.forEach(function (repo) {
                        var repoTemplate = $('#repo-template').html();
                        var item = repoTemplate.replace(/\[(.*?)\]/g, function () {
                            return eval(arguments[1]);
                        });
                        projects.append(item)
                    })
                });
        }
    }

    window.Xblog = Xblog;
})(jQuery);
$(document).ready(function () {
    Xblog.init();
});
function replySomeone(username) {
    var commentContent = $("#comment-content");
    var oldContent = commentContent.val();
    prefix = "@" + username + " ";
    var newContent = '';
    if (oldContent.length > 0) {
        newContent = oldContent + "\n" + prefix;
    } else {
        newContent = prefix
    }
    commentContent.focus();
    commentContent.val(newContent);
    moveEnd(commentContent);
}

var moveEnd = function (obj) {
    obj.focus();
    var len = obj.value === undefined ? 0 : obj.value.length;

    if (document.selection) {
        var sel = obj.createTextRange();
        sel.moveStart('character', len);
        sel.collapse();
        sel.select();
    } else if (typeof obj.selectionStart == 'number' && typeof obj.selectionEnd == 'number') {
        obj.selectionStart = obj.selectionEnd = len;
    }
};