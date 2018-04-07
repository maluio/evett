<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EventRepository")
 * @UniqueEntity("uniqueIdentifier")
 */
class Event
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @var string
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     * @Assert\DateTime()
     * @Assert\NotBlank()
     */
    private $start;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     * @Assert\DateTime()
     */
    private $end;

    /**
     * @ORM\Column(type="string", unique=true)
     * @var string
     * @Assert\NotBlank()
     */
    private $url;

    /**
     * @ORM\Column(type="string")
     * @var string
     * @Assert\NotBlank()
     */
    private $provider;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $uniqueIdentifier;

    /**
     * @ORM\Column(type="boolean")
     * @var boolean
     */
    private $starred = false;

    /**
     * @ORM\Column(type="boolean")
     * @var boolean
     */
    private $hidden = false;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     * @Assert\DateTime()
     * @Assert\NotBlank()
     */
    private $updated;

    public function __construct()
    {
        $this->setHidden(false);
        $this->setStarred(false);
        $this->setUpdated(new \DateTime());
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param \DateTime $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

    /**
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @param \DateTime $end
     */
    public function setEnd($end)
    {
        $this->end = $end;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @param string $provider
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;
    }

    /**
     * @return bool
     */
    public function isStarred()
    {
        return $this->starred;
    }

    /**
     * @param bool $starred
     */
    public function setStarred($starred)
    {
        $this->starred = $starred;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return $this->hidden;
    }

    /**
     * @param bool $hidden
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * @return string
     */
    public function getUniqueIdentifier(): string
    {
        return $this->uniqueIdentifier;
    }

    /**
     * @param string $uniqueIdentifier
     */
    public function setUniqueIdentifier(string $uniqueIdentifier): void
    {
        $this->uniqueIdentifier = $uniqueIdentifier;
    }
}
