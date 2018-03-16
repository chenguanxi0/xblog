<?php
/**
 * Created by PhpStorm.
 * User: lufficc
 * Date: 2016/9/20
 * Time: 18:02
 */

namespace Lufficc\ExceptionHandler;

use Exception;
use Illuminate\Http\Request;
use Lufficc\Exception\CommentNotAllowedException;

class BlogExceptionHandler
{
    /**
     * @param $request
     * @param Exception $exception
     * @return mixed
     */
    public function handler(Request $request, Exception $exception)
    {
        if ($request->expectsJson()) {
            $msg = 'Sorry, something went wrong.';
            if ($exception instanceof CommentNotAllowedException) {
                $msg = 'Sorry, comment is not allowed now.';
            }
            return response()->json(
                ['status' => $exception->getCode(), 'msg' => $msg]
            );
        }
        return false;
    }
}