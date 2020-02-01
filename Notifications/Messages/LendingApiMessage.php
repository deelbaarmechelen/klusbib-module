<?php

namespace Modules\Klusbib\Notifications\Messages;


class LendingApiMessage
{
    private $startDate;
    private $dueDate;
    private $returnDate;
    private $toolId;
    private $userId;
    private $comments;
    private $createdBy;

    private $target;
    private $item;
    private $admin;

    /**
     * LendingApiMessage constructor.
     * @param $target
     * @param $item
     * @param $note
     */
    public function __construct($target, $item, $note, $admin)
    {
        $this->target = $target;
        $this->item = $item;
        $this->comments = $note;
        $this->admin = $admin;
    }

    /**
     * Set the start date of the lending.
     *
     * @param  \DateTime $startDate
     * @return $this
     */
    public function startDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }
    /**
     * Set the due date of the lending.
     *
     * @param  \DateTime $dueDate
     * @return $this
     */
    public function dueDate($dueDate)
    {
        $this->dueDate = $dueDate;

        return $this;
    }
    /**
     * Set the tool id of the lending.
     *
     * @param  $toolId
     * @return $this
     */
    public function toolId($toolId)
    {
        $this->toolId = $toolId;

        return $this;
    }
    /**
     * Set the user id of the lending.
     *
     * @param  $toolId
     * @return $this
     */
    public function userId($userId)
    {
        $this->userId = $userId;

        return $this;
    }
    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return mixed
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }

    /**
     * @return mixed
     */
    public function getReturnDate()
    {
        return $this->returnDate;
    }

    /**
     * @return mixed
     */
    public function getToolId()
    {
        return $this->toolId;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return mixed
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

}