<?php

namespace Modules\Klusbib\Notifications\Messages;


class LendingApiMessage
{
    public const METHOD_CREATE = "POST";
    public const METHOD_UPDATE = "PUT";
    public const TOOL_TYPE_ASSET = "TOOL";
    public const TOOL_TYPE_ACCESSORY = "ACCESSORY";

    private $startDate;
    private $dueDate;
    private $returnedDate;
    private $toolId;
    private $toolType;
    private $userId;
    private $comments;
    private $createdBy;

    private $method;
    private $target;
    private $item;
    private $admin;

    /**
     * LendingApiMessage constructor.
     * @param $target
     * @param $item
     * @param $note
     */
    public function __construct($method, $target, $item, $note, $admin)
    {
        $this->target = $target;
        $this->item = $item;
        $this->comments = $note;
        $this->admin = $admin;
        $this->method = $method;
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
     * Set the returned date of the lending.
     *
     * @param  \DateTime $returnedDate
     * @return $this
     */
    public function returnedDate($returnedDate)
    {
        $this->returnedDate = $returnedDate;

        return $this;
    }
    /**
     * Set the tool id and type of the lending.
     *
     * @param  $toolId
     * @return $this
     */
    public function tool($toolId, $toolType)
    {
        $this->toolId = $toolId;
        $this->toolType = $toolType;

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
    public function getMethod()
    {
        return $this->method;
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
    public function getReturnedDate()
    {
        return $this->returnedDate;
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
    public function getToolType()
    {
        if (isset($this->toolType)) {
            return $this->toolType;
        }
        return self::TOOL_TYPE_ASSET;
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