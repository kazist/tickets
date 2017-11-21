<?php

namespace Tickets\Tickets\Comments\Code\Tables;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comments
 *
 * @ORM\Table(name="tickets_tickets_comments", indexes={@ORM\Index(name="ticket_id_index", columns={"ticket_id"}), @ORM\Index(name="modified_by_index", columns={"modified_by"}), @ORM\Index(name="created_by_index", columns={"created_by"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Comments extends \Kazist\Table\BaseTable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="comment", type="text", nullable=false)
     */
    protected $comment;

    /**
     * @var integer
     *
     * @ORM\Column(name="ticket_id", type="integer", length=11, nullable=false)
     */
    protected $ticket_id;

    /**
     * @var integer
     *
     * @ORM\Column(name="attachment", type="integer", length=11, nullable=true)
     */
    protected $attachment;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_support", type="integer", length=11, nullable=true)
     */
    protected $is_support;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_created", type="datetime", nullable=true)
     */
    protected $date_created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_modified", type="datetime", nullable=true)
     */
    protected $date_modified;

    /**
     * @var integer
     *
     * @ORM\Column(name="modified_by", type="integer", length=11, nullable=true)
     */
    protected $modified_by;

    /**
     * @var integer
     *
     * @ORM\Column(name="created_by", type="integer", length=11, nullable=true)
     */
    protected $created_by;


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
     * Set comment
     *
     * @param string $comment
     *
     * @return Comments
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Set ticketId
     *
     * @param integer $ticketId
     *
     * @return Comments
     */
    public function setTicketId($ticketId)
    {
        $this->ticket_id = $ticketId;

        return $this;
    }

    /**
     * Get ticketId
     *
     * @return integer
     */
    public function getTicketId()
    {
        return $this->ticket_id;
    }

    /**
     * Set attachment
     *
     * @param integer $attachment
     *
     * @return Comments
     */
    public function setAttachment($attachment)
    {
        $this->attachment = $attachment;

        return $this;
    }

    /**
     * Get attachment
     *
     * @return integer
     */
    public function getAttachment()
    {
        return $this->attachment;
    }

    /**
     * Set isSupport
     *
     * @param integer $isSupport
     *
     * @return Comments
     */
    public function setIsSupport($isSupport)
    {
        $this->is_support = $isSupport;

        return $this;
    }

    /**
     * Get isSupport
     *
     * @return integer
     */
    public function getIsSupport()
    {
        return $this->is_support;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->date_created;
    }

    /**
     * Get dateModified
     *
     * @return \DateTime
     */
    public function getDateModified()
    {
        return $this->date_modified;
    }

    /**
     * Get modifiedBy
     *
     * @return integer
     */
    public function getModifiedBy()
    {
        return $this->modified_by;
    }

    /**
     * Get createdBy
     *
     * @return integer
     */
    public function getCreatedBy()
    {
        return $this->created_by;
    }
    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        // Add your code here
    }
}

