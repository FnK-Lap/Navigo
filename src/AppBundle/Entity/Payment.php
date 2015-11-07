<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Payment
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Entity\PaymentRepository")
 */
class Payment
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
     * @var \DateTime
     *
     * @ORM\Column(name="paid_at", type="datetime")
     */
    private $paidAt;


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
     * Set paidAt
     *
     * @param \DateTime $paidAt
     *
     * @return Payment
     */
    public function setPaidAt($paidAt)
    {
        $this->paidAt = $paidAt;

        return $this;
    }

    /**
     * Get paidAt
     *
     * @return \DateTime
     */
    public function getPaidAt()
    {
        return $this->paidAt;
    }
}

