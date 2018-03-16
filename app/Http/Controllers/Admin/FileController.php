<?php

namespace App\Http\Controllers\Admin;

use App\File;
use App\Http\Controllers\Controller;
use App\Http\Repositories\ImageRepository;
use App\Http\Repositories\UnknownFileRepository;
use Illuminate\Http\Request;

class FileController extends Controller
{
    protected $imageRepository;
    protected $unknownFileRepository;

    /**
     * ImageController constructor.
     * @param ImageRepository $imageRepository
     * @param UnknownFileRepository $unknownFileRepository
     */
    public function __construct(ImageRepository $imageRepository,
                                UnknownFileRepository $unknownFileRepository)
    {
        $this->imageRepository = $imageRepository;
        $this->unknownFileRepository = $unknownFileRepository;
    }

    public function files()
    {
        $files = $this->unknownFileRepository->getAllFiles();
        return view('admin.files', compact('files'));
    }


    public function uploadFile(Request $request)
    {
        $this->validate($request, [
            'file' => 'required',
        ]);
        $type = $request->get('type');

        if ($type) {
            return $this->uploadTypeFile($request);
        }

        if ($this->upload($request, $this->getTag($request)))
            return back()->with('success', '上传成功');
        return back()->withErrors('上传失败');
    }

    public function uploadTypeFile(Request $request)
    {
        $this->validate($request, [
            'file' => 'required',
            'type' => 'required',
        ]);
        if ($this->upload($request, $request->get('type')))
            return back()->with('success', '上传成功');
        return back()->withErrors('上传失败');
    }

    public function deleteFile(Request $request)
    {
        $key = $request->get('key');
        $type = File::where('key', $key)->firstOrFail()->type;
        $this->unknownFileRepository->setTag($type);
        $result = $this->unknownFileRepository->delete($key);
        if ($result) {
            return back()->with('success', '删除成功');
        }
        return back()->with('success', '删除失败');
    }

    private function upload(Request $request, $type)
    {
        $this->unknownFileRepository->setTag($type);
        return $this->unknownFileRepository->upload($request);
    }

    private function getTag(Request $request)
    {
        $tag = $request->file('file')->getClientOriginalExtension();
        if (!$tag) {
            $tag = 'unknown';
        }
        return $tag;
    }
}
