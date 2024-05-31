<?php

namespace KingBes\PhpWebview;

use FFI;
use OsException;

class Dialog
{
    private FFI $ffi;

    public function __construct(
        protected string         $baseDir = __DIR__,
    ) {
        $header = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'dialog_php.h');
        $this->ffi = FFI::cdef($header, $this->getDefaultLibraryFile());
    }

    /**
     * 消息框 function
     *
     * @param string $str 消息内容
     * @param integer $type 类型 0~2
     * @return boolean
     */
    public function msg(string $str, int $type = 0): bool
    {
        $b = $this->ffi->message($str, $type);
        return $b;
    }

    /**
     * 输入框 function
     *
     * @return string
     */
    public function prompt(): string
    {
        $obj = $this->ffi->prompt("");
        return $obj->str;
    }

    /**
     * 打开文件 function
     *
     * @return string
     */
    public function file(): string
    {
        $obj = $this->ffi->file_dialog();
        return $obj->str;
    }

    /**
     * 打开文件夹 function
     *
     * @param string $dirs 初始文件夹路径,比如：`D:/XXX`  =>D盘的xxx文件夹
     * @return string
     */
    public function dir(string $dirs = ""): string
    {
        $obj = $this->ffi->open_dir($dirs);
        return $obj->str;
    }

    /**
     * 保存文件 function
     *
     * @param string $content 保存内容
     * @param string $filename 文件名 比如 xxx.txt
     * @param string $path 初始文件夹路径,比如：`D:/XXX`  =>D盘的xxx文件夹
     * @return boolean
     */
    public function save(string $content, string $filename, string $path = ""): bool
    {
        return $this->ffi->save_file($content, $path, $filename);
    }

    private function getDefaultLibraryFile(): string
    {
        $pharPath = \Phar::running(false);
        if ($pharPath != "") {
            $dirPath = dirname($pharPath) . DIRECTORY_SEPARATOR . 'os' . DIRECTORY_SEPARATOR;
        } else {
            $dirPath = dirname($this->baseDir) . DIRECTORY_SEPARATOR . 'os' . DIRECTORY_SEPARATOR;
        }
        $libraryFile = match (PHP_OS_FAMILY) {
            /* 'Linux'   => $dirPath . 'linux' . DIRECTORY_SEPARATOR . 'webview_php_ffi.so',
            'Darwin'  => $dirPath . 'macos' . DIRECTORY_SEPARATOR . 'webview_php_ffi.dylib', */
            'Windows' => $dirPath . 'windows' . DIRECTORY_SEPARATOR . 'dialog.dll',
            default   => throw OsException::OsNotSupported(),
        };
        return $libraryFile;
    }
}
