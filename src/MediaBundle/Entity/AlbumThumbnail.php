<?php
/**
 * Created by PhpStorm.
 * User: ktlle
 * Date: 10/1/17
 * Time: 11:29 PM
 */

namespace MediaBundle\Entity;


class AlbumThumbnail
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\OneToOne(targetEntity="MediaBundle\Entity\Album")
     * @ORM\JoinColumn(name="album_id", referencedColumnName="id")
     */
    private $album;
    /**
     * @ORM\OneToOne(targetEntity="MediaBundle\Entity\Image")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     */
    private $image;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getAlbum()
    {
        return $this->album;
    }

    /**
     * @param mixed $album
     */
    public function setAlbum($album)
    {
        $this->album = $album;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }
}