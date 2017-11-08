<?php

class Response
{

    private $id;
    private $survey;
    private $title;
    private $count;
    private $percentage;

    public function __construct($survey, $title, $count = 0)
    {
        $this->id     = null;
        $this->survey = $survey;
        $this->title  = $title;
        $this->count  = $count;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function computePercentage($total)
    {
        if ($total === 0) {
            $percentage = 0;

            return $percentage;
        } else {
            $percentage = $this->count * 100 / $total;

            return round($percentage);
        }

    }

    public function getId()
    {
        return $this->id;
    }

    public function getSurvey()
    {
        return $this->survey;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getCount()
    {
        return $this->count;
    }

    public function getPercentage()
    {
        return $this->percentage;
    }

}

?>
