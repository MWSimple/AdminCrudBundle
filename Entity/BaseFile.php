<?php

namespace MWSimple\Bundle\AdminCrudBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Abstract File Base
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 *
 * @author MWS
 */
class BaseFile
{
    /**
     * @Assert\File()
     */
    protected $file;

    /**
     * @var string
     *
     * @ORM\Column(name="file_path", type="string", length=255, nullable=true)
     */
    protected $filePath;

    /**
     * @var string
     */
    protected $temp;

    /**
     * @var string
     */
    protected $uploadDir;

    /**
     * Set filePath
     *
     * @param  string $filePath
     * @return File
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;

        return $this;
    }

    /**
     * Get FilePath
     *
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * Set file
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        // check if we have an old image path
        if (is_file($this->getAbsolutePath())) {
            // store the old name to delete after the update
            $this->temp = $this->getAbsolutePath();
        }
    }

    /**
     * Get file
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    public function getAbsolutePath()
    {
        return is_null($this->filePath)
            ? null
            : $this->getUploadRootDir().'/'.$this->filePath
        ;
    }

    public function getWebPath()
    {
        return is_null($this->filePath)
            ? null
            : $this->getUploadDir().'/'.$this->filePath
        ;
    }

    protected function getUploadRootDir()
    {
        if (!$this->getUploadDir()) {
            $uploadDir = 'uploads';
        } else {
            $uploadDir = $this->getUploadDir();
        }

        $path = __DIR__ . '/../../../../../../../web/' . $this->getUploadDir();
        if (!file_exists($path)) {
            mkdir($path, 0755);
        }

        return $path;
    }

    public function setUploadDir($uploadDir)
    {
        $this->uploadDir = $uploadDir;
    }

    public function getUploadDir()
    {
        return $this->uploadDir;
    }

    public function getFixturesPath()
    {
        return $this->getAbsolutePath() . 'web/filefixture/';
    }

    /**
     * @ORM\PreFlush()
     */
    public function preUpload()
    {
        if (!is_null($this->getFile())) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));
            $this->filePath = $filename . '.' . $this->getFile()->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (is_null($this->getFile())) {
            return null;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getFile()->move($this->getUploadRootDir(), $this->filePath);
        // check if we have an old image
        if (isset($this->temp)) {
            if (file_exists($this->temp)) {
                // delete the old image
                unlink($this->temp);
            }
            // clear the temp image path
            $this->temp = null;
        }
        $this->file = null;
    }

    /**
     * @ORM\PreRemove()
     */
    public function storeFilenameForRemove()
    {
        $this->temp = $this->getAbsolutePath();
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if (isset($this->temp)) {
            if (file_exists($this->temp)) {
                unlink($this->temp);
            }
        }
    }
}