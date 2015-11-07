<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Subscription
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\SubscriptionRepository")
 */
class Subscription
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="duration", type="integer")
     */
    private $duration;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="subscribed_at", type="datetime")
     */
    private $subscribedAt;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set duration
     *
     * @param integer $duration
     *
     * @return Subscription
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return integer
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set subscribedAt
     *
     * @param \DateTime $subscribedAt
     *
     * @return Subscription
     */
    public function setSubscribedAt($subscribedAt)
    {
        $this->subscribedAt = $subscribedAt;

        return $this;
    }

    /**
     * Get subscribedAt
     *
     * @return \DateTime
     */
    public function getSubscribedAt()
    {
        return $this->subscribedAt;
    }
}

