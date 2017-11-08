<?php

class Survey
{

    private $id;
    private $owner;
    private $question;
    private $responses;

    public function __construct($owner, $question)
    {
        $this->id        = null;
        $this->owner     = $owner;
        $this->question  = $question;
        $this->responses = [];
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function getQuestion()
    {
        return $this->question;
    }

    public function &getResponses()
    {
        return $this->responses;
    }

    public function setResponses($responses)
    {
        $this->responses = $responses;
    }

    public function addResponse($response)
    {
        $this->responses[] = $response;
    }

    public function computePercentages()
    {
        $replies     = $this->getResponses();
        $question    = $this->getQuestion();
        $totalCount  = 0;
        $percentages = [];
        foreach ($replies as $count) {
            $totalCount += $count['count'];
        }

        foreach ($replies as $rep) {
            $newReponse    = new Response($question, $rep['title'], $rep['count']);
            $percentage    = $newReponse->computePercentage($totalCount);
            $percentages[] = $percentage;
        }

        return $percentages;
    }

}
