<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvcTests\http\fixture;

use bovigo\vfs\vfsDirectory;
use bovigo\vfs\vfsStream;
use bovigo\vfs\vfsStreamContent;

/**
 * Class MockFilesysFixture.
 */
class MockFilesysFixture
{
    protected vfsDirectory $vfsFileSys;

    protected string $vfsRoot;

    protected array $allFilesAndDirectories
        = [
            'vfs://root',
            'vfs://root/Subdir_1',
            'vfs://root/Subdir_1/AbstractFactory',
            'vfs://root/Subdir_1/AbstractFactory/test.php',
            'vfs://root/Subdir_1/AbstractFactory/other.php',
            'vfs://root/Subdir_1/AbstractFactory/Invalid.csv',
            'vfs://root/Subdir_1/AbstractFactory/valid.css',
            'vfs://root/Subdir_1/AnEmptyFolder',
            'vfs://root/Subdir_1/somecode.php',
            'vfs://root/Subdir_1/somejavascript.js',
            'vfs://root/Subdir_2',
            'vfs://root/Subdir_2/SmallLibrary',
            'vfs://root/Subdir_2/SmallLibrary/libFile_1.php',
            'vfs://root/Subdir_2/SmallLibrary/libFile_2.php',
            'vfs://root/Subdir_2/SmallLibrary/libFile.css',
            'vfs://root/Subdir_2/SmallLibrary/libFile.js',
            'vfs://root/Subdir_2/SmallLibrary/libFileDoc.txt',
            'vfs://root/Subdir_2/SmallLibrary/OtherJSFile.js',
            'vfs://root/Subdir_2/SmallLibrary/libFile_3.php',
            'vfs://root/Subdir_2/SmallLibrary/libFile_4.php',
            'vfs://root/fileInRootOfFixture.ini'
        ];

    protected array $allFilesAndDirectoriesBreadthFirst
        = [
            'vfs://root',

            'vfs://root/Subdir_1',
            'vfs://root/Subdir_2',
            'vfs://root/fileInRootOfFixture.ini',

            'vfs://root/Subdir_1/AbstractFactory',
            'vfs://root/Subdir_1/AnEmptyFolder',
            'vfs://root/Subdir_1/somecode.php',
            'vfs://root/Subdir_1/somejavascript.js',
            'vfs://root/Subdir_2/SmallLibrary',

            'vfs://root/Subdir_1/AbstractFactory/test.php',
            'vfs://root/Subdir_1/AbstractFactory/other.php',
            'vfs://root/Subdir_1/AbstractFactory/Invalid.csv',
            'vfs://root/Subdir_1/AbstractFactory/valid.css',

            'vfs://root/Subdir_2/SmallLibrary/libFile_1.php',
            'vfs://root/Subdir_2/SmallLibrary/libFile_2.php',
            'vfs://root/Subdir_2/SmallLibrary/libFile.css',
            'vfs://root/Subdir_2/SmallLibrary/libFile.js',
            'vfs://root/Subdir_2/SmallLibrary/libFileDoc.txt',
            'vfs://root/Subdir_2/SmallLibrary/OtherJSFile.js',
            'vfs://root/Subdir_2/SmallLibrary/libFile_3.php',
            'vfs://root/Subdir_2/SmallLibrary/libFile_4.php',
        ];

    /**
     * @var string[]
     */
    protected array $allFiles
        = [
            'vfs://root/Subdir_1/AbstractFactory/test.php',
            'vfs://root/Subdir_1/AbstractFactory/other.php',
            'vfs://root/Subdir_1/AbstractFactory/Invalid.csv',
            'vfs://root/Subdir_1/AbstractFactory/valid.css',
            'vfs://root/Subdir_1/somecode.php',
            'vfs://root/Subdir_1/somejavascript.js',
            'vfs://root/Subdir_2/SmallLibrary/libFile_1.php',
            'vfs://root/Subdir_2/SmallLibrary/libFile_2.php',
            'vfs://root/Subdir_2/SmallLibrary/libFile.css',
            'vfs://root/Subdir_2/SmallLibrary/libFile.js',
            'vfs://root/Subdir_2/SmallLibrary/libFileDoc.txt',
            'vfs://root/Subdir_2/SmallLibrary/OtherJSFile.js',
            'vfs://root/Subdir_2/SmallLibrary/libFile_3.php',
            'vfs://root/Subdir_2/SmallLibrary/libFile_4.php',
            'vfs://root/fileInRootOfFixture.ini'
        ];

    /**
     * @var string[]
     */
    protected array $phpFiles
        = [
            'vfs://root/Subdir_1/AbstractFactory/test.php',
            'vfs://root/Subdir_1/AbstractFactory/other.php',
            'vfs://root/Subdir_1/somecode.php',
            'vfs://root/Subdir_2/SmallLibrary/libFile_1.php',
            'vfs://root/Subdir_2/SmallLibrary/libFile_2.php',
            'vfs://root/Subdir_2/SmallLibrary/libFile_3.php',
            'vfs://root/Subdir_2/SmallLibrary/libFile_4.php'
        ];

    /**
     * @var string[]
     */
    protected array $jsFiles
        = [
            'vfs://root/Subdir_1/somejavascript.js',
            'vfs://root/Subdir_2/SmallLibrary/libFile.js',
            'vfs://root/Subdir_2/SmallLibrary/OtherJSFile.js'
        ];

    /**
     * @var string[]
     */
    protected array $cssCsvFiles
        = [
            'vfs://root/Subdir_1/AbstractFactory/Invalid.csv',
            'vfs://root/Subdir_1/AbstractFactory/valid.css',
            'vfs://root/Subdir_2/SmallLibrary/libFile.css'
        ];

    protected vfsStreamContent $vfsDirectory;
    protected vfsStreamContent $vfsEmptyDirectory;
    protected vfsStreamContent $vfsFile;
    protected vfsStreamContent $vfsFileAdditional;

    protected string $urlDirectoryWithFiles;
    protected string $urlDirectoryEmpty;
    protected string $urlDirectoryNonExistent = 'bar';
    protected string $urlFile;
    protected string $urlFileAdditional;
    protected string $urlFileNonExistent = 'foo';
    protected array $urlFilesContainingTheWordThis
        = [
            'vfs://root/Subdir_1/somejavascript.js',
            'vfs://root/Subdir_2/SmallLibrary/libFile_1.php',
            'vfs://root/Subdir_2/SmallLibrary/libFile_2.php',
            'vfs://root/Subdir_2/SmallLibrary/libFile.css',
            'vfs://root/Subdir_2/SmallLibrary/libFile.js',
            'vfs://root/Subdir_2/SmallLibrary/libFileDoc.txt',
            'vfs://root/fileInRootOfFixture.ini'
        ];

    protected int $expectedNumberOfDirectoryEntriesWithoutDots = 4;


    /**
     * MockFilesysFixture constructor.
     */
    public function __construct()
    {
        /** @var array<string|array<string>> $arrSrcFiles */
        $arrSrcFiles = [
            'Subdir_1'                => [
                'AbstractFactory'   => [
                    'test.php'    => 'some text content',
                    'other.php'   => 'Some more text content',
                    'Invalid.csv' => 'Something else',
                    'valid.css'   => 'not real css'
                ],
                'AnEmptyFolder'     => [],
                'somecode.php'      => 'some php content',
                'somejavascript.js' => 'this is not real javascript - it is just a test'
            ],
            'Subdir_2'                => [
                'SmallLibrary' => [
                    'libFile_1.php'  => 'This is another php file of some kind',
                    'libFile_2.php'  => 'This is the second php file in this library.',
                    'libFile.css'    => 'This is the first css file in this library.',
                    'libFile.js'     => 'This is the first javascript file in this library.',
                    'libFileDoc.txt' => 'This should be some documentation kind of stuff.',
                    'OtherJSFile.js' => 'more bogus javascript',
                    'libFile_3.php'  => 'libFile_3.php content',
                    'libFile_4.php'  => 'libFile_4.php content'
                ]
            ],
            'fileInRootOfFixture.ini' => 'Maybe this is some kind of a configuration file... or not'
        ];

        $filesysRoot = 'root';
        $permissions = null;

        /**
         * type vfsStreamDirectory
         */
        $this->vfsFileSys = vfsStream::setup($filesysRoot, $permissions, $arrSrcFiles);

        /**
         * type vfsStreamContent
         */
        $this->vfsDirectory = $this->vfsFileSys->getChild('Subdir_1');
        $this->vfsEmptyDirectory = $this->vfsFileSys->getChild('Subdir_1/AnEmptyFolder');
        $this->vfsFile = $this->vfsFileSys->getChild('Subdir_1/somecode.php');
        $this->vfsFileAdditional = $this->vfsFileSys->getChild('Subdir_1/somejavascript.js');

        /**
         * type string
         */
        $this->vfsRoot = $this->vfsFileSys->url();

        $this->urlDirectoryWithFiles = $this->vfsDirectory->url();
        $this->urlDirectoryEmpty = $this->vfsEmptyDirectory->url();

        $this->urlFile = $this->vfsFile->url();
        $this->urlFileAdditional = $this->vfsFileAdditional->url();
    }

    /**
     * @function findVfsFiles
     * @param vfsStreamContent $vfsStreamContent
     * @param string $regex
     * @return array<string|array<string>>
     */
    public function findVfsFiles(vfsStreamContent $vfsStreamContent, string $regex): array
    {
        $files = [];

        if (($vfsStreamContent->getType() == vfsStreamContent::TYPE_FILE) &&
            (preg_match($regex, $vfsStreamContent->url()))) {
            $files[] = $vfsStreamContent->url();
            return $files;
        }

        if ($vfsStreamContent instanceof vfsStreamDirectory) {
            $childIterator = $vfsStreamContent->getChildren();
            foreach ($childIterator as $file) {
                if (($file instanceof vfsStreamDirectory) && !$file->isDot()) {
                    $files = array_merge($files, $this->findVfsFiles($file, $regex));
                } elseif (preg_match($regex, (string)$file->url())) {
                    $files[] = $file->url();
                }
            }
        }
        return $files;
    }

    /**
     * getVfsRoot
     * @return string
     */
    public function getVfsRoot(): string
    {
        return $this->vfsRoot;
    }

    /**
     * changePermissionsOnRootToUnreadable
     */
    public function changePermissionsOnRootToUnreadable(): void
    {
        $this->getVfsFileSys()->chmod(0000);
    }

    /**
     * getVfsFileSys
     * @return vfsStreamDirectory
     */
    public function getVfsFileSys(): vfsStreamDirectory
    {
        return $this->vfsFileSys;
    }

    public function getAllFilesAndDirectories(): array
    {
        return $this->allFilesAndDirectories;
    }

    public function getAllFilesAndDirectoriesBreadthFirst(): array
    {
        return $this->allFilesAndDirectoriesBreadthFirst;
    }

    /**
     * getAllFiles
     * @return array<string|array<string>>
     */
    public function getAllFiles(): array
    {
        return $this->allFiles;
    }

    /**
     * getPhpFiles
     * @return array<string|array<string>>
     */
    public function getPhpFiles(): array
    {
        return $this->phpFiles;
    }

    /**
     * getJsFiles
     * @return array<string|array<string>>
     */
    public function getJsFiles(): array
    {
        return $this->jsFiles;
    }

    /**
     * getCssCsvFiles
     * @return array<string|array<string>>
     */
    public function getCssCsvFiles(): array
    {
        return $this->cssCsvFiles;
    }

    /**
     * getUrlFilesContainingTheWordThis
     * @return array<string|array<string>>
     */
    public function getUrlFilesContainingTheWordThis(): array
    {
        return $this->urlFilesContainingTheWordThis;
    }

    /**
     * @return vfsStreamContent
     */
    public function getVfsFile(): vfsStreamContent
    {
        return $this->vfsFile;
    }

    /**
     * @return vfsStreamContent
     */
    public function getVfsDirectory(): vfsStreamContent
    {
        return $this->vfsDirectory;
    }

    /**
     * @return string
     */
    public function getUrlFile(): string
    {
        return $this->urlFile;
    }

    /**
     * @return string
     */
    public function getUrlFileAdditional(): string
    {
        return $this->urlFileAdditional;
    }

    /**
     * @return string
     */
    public function getUrlFileNonExistent(): string
    {
        return $this->urlFileNonExistent;
    }

    /**
     * @return string
     */
    public function getUrlDirectoryWithFiles(): string
    {
        return $this->urlDirectoryWithFiles;
    }

    /**
     * @return int
     */
    public function getExpectedNumberOfDirectoryEntriesWithoutDots(): int
    {
        return $this->expectedNumberOfDirectoryEntriesWithoutDots;
    }

    /**
     * @return string
     */
    public function getUrlDirectoryEmpty(): string
    {
        return $this->urlDirectoryEmpty;
    }

    /**
     * @return string
     */
    public function getUrlDirectoryNonExistent(): string
    {
        return $this->urlDirectoryNonExistent;
    }
}
