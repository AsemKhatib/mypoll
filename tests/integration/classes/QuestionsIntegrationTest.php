<?php

namespace MyPoll\Tests\Integration\Classes;

use DI\Container;
use Mockery as m;
use MyPoll\Classes\Questions;
use Twig_Loader_Filesystem;
use PHPUnit_Framework_TestCase;
use Exception;

class QuestionsIntegrationTest extends PHPUnit_Framework_TestCase
{
    protected $qid = 46;

    protected $dataArray = array(
        'question' => 'question-test-Add',
        'answers' => array('answer1', 'answer2', 'answer3')
    );

    protected $dataArrayEdit = array(
        'qid' => 46,
        'question' => 'question-test-edit',
        'answers_old' => array('answer1', 'answer2', 'answer3')
    );

    /**
     * @var Container
     */
    private $container;

    /**
     * @var Twig_Loader_Filesystem
     */
    private $twigLoader;

    public function setUp()
    {
        parent::setUp();
        global  $container;
        $this->container = $container;
        $this->twigLoader = $this->container->get(Twig_Loader_Filesystem::class);
    }

    public function tearDown()
    {
        parent::tearDown(); // TODO: Change the autogenerated stub
    }

    /**
     * @return Questions
     */
    private function getQuestion()
    {
        return $this->container->get(Questions::class);
    }

    public function testAddExecuteSuccess()
    {
        $question = $this->getQuestion();
        $this->assertEquals('Question Added successfully', $question->addExecute($this->dataArray));
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Something went wrong while trying to add the question
     */
    public function testAddExecuteFailWithAddQuestion()
    {
        $this->dataArray['question'] = '';
        $this->getQuestion()->addExecute($this->dataArray);
    }

    /**
     * @expectedException \Exception
     * @expectedExceptionMessage Something went wrong while trying to add the answers of the new question
     */
    public function testAddExecuteFailWithAddAnswers()
    {
        $this->dataArray['ansers'] = array();
        $this->getQuestion()->addExecute($this->dataArray);
    }


    public function testShowSuccess()
    {
        $this->twigLoader->addPath('admin/template/');
        $this->assertContains('<div class="show_poll">', $this->getQuestion()->show());
    }

    /**
     * @expectedException \Twig_Error_Loader
     */
    public function testShowFailWithTwig()
    {
        $this->getQuestion()->show();
    }

    public function testShowAnswersSuccess()
    {
        $this->twigLoader->addPath('admin/template/');
        $this->assertContains(
            'function drawChart()',
            $this->getQuestion()->showAnswers($this->qid, true)
        );
    }

    /**
     * @expectedException \Twig_Error_Loader
     */
    public function testShowAnswersFailWithTwig()
    {
        $this->getQuestion()->showAnswers($this->qid, 'true');
    }

    public function testEditSuccess()
    {
        $this->twigLoader->addPath('admin/template/');
        $this->assertContains(
            '<input type="hidden" id="callBack" value="editExecute">',
            $this->getQuestion()->edit($this->qid)
        );
    }

    /**
     * @expectedException \Twig_Error_Loader
     */
    public function testEditFail()
    {
        $this->getQuestion()->edit($this->qid+100);
    }

    public function testEditExecuteSuccess()
    {
        $question = $this->getQuestion();
        $this->assertEquals('Question edited successfully', $question->editExecute($this->dataArrayEdit));
    }

    public function testDeleteSuccess()
    {
        $this->assertEquals(
            'The question and all its answers were successfully deleted',
            $this->getQuestion()->delete($this->qid)
        );
    }

}
