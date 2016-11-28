<?php
namespace MoufTest\Controllers;

use Mouf\Mvc\Splash\Annotations\Get;
use Mouf\Mvc\Splash\Annotations\Post;
use Mouf\Mvc\Splash\Annotations\Put;
use Mouf\Mvc\Splash\Annotations\Delete;
use Mouf\Mvc\Splash\Annotations\URL;
use Mouf\Html\Template\TemplateInterface;
use Mouf\Html\HtmlElement\HtmlBlock;
use Psr\Log\LoggerInterface;
use MoufTest\Model\Dao\Generated\DaoFactory;
use \Twig_Environment;
use Mouf\Html\Renderer\Twig\TwigTemplate;
use Mouf\Mvc\Splash\HtmlResponse;

use Zend\Diactoros\Response\RedirectResponse;
use Zend\Diactoros\Response\JsonResponse;
use Mouf\Html\Utils\WebLibraryManager\WebLibrary;

use MoufTest\Model\Bean\CarBean;

/**
 * TODO: write controller comment
 */
class CarController {

  /**
   * The logger used by this controller.
   * @var LoggerInterface
   */
  private $logger;

  /**
   * The template used by this controller.
   * @var TemplateInterface
   */
  private $template;

  /**
   * The main content block of the page.
   * @var HtmlBlock
   */
  private $content;

  /**
   * The DAO factory object.
   * @var DaoFactory
   */
  private $daoFactory;

  /**
   * The Twig environment (used to render Twig templates).
   * @var Twig_Environment
   */
  private $twig;


  /**
   * Controller's constructor.
   * @param LoggerInterface $logger The logger
   * @param TemplateInterface $template The template used by this controller
   * @param HtmlBlock $content The main content block of the page
   * @param DaoFactory $daoFactory The object in charge of retrieving DAOs
   * @param Twig_Environment $twig The Twig environment (used to render Twig templates)
   */
  public function __construct(LoggerInterface $logger, TemplateInterface $template, HtmlBlock $content, DaoFactory $daoFactory, Twig_Environment $twig) {
      $this->logger = $logger;
      $this->template = $template;
      $this->content = $content;
      $this->daoFactory = $daoFactory;
      $this->twig = $twig;
  }

  /**
   * @URL("cars")

   * @Get
   */
  public function index() {

    // Get all cars 
    $cars = $this->daoFactory->getCarDao()->findAll()->jsonSerialize();

    // Add js and css files
    $webLibraryManager = \Mouf::getDefaultWebLibraryManager();
    $webLibraryManager->addJsFile('public/js/jquery.dataTables.min.js');
    $webLibraryManager->addJsFile('public/js/mouf_test.js');
    $webLibraryManager->addCssFile('public/css/jquery.dataTables.min.css');

    // Let's add the twig file to the template.
    $this->content->addHtmlElement(new TwigTemplate($this->twig, 'views/car/index.twig', array(
      "cars" => $cars,
    )));

    return new HtmlResponse($this->template);
  }
  /**
   * @URL("car/create")

   * @Get
   */
  public function create() {

    // Get all brands
    $brands = $this->daoFactory->getBrandDao()->findAll()->jsonSerialize();

    // Add js files
    $webLibraryManager = \Mouf::getDefaultWebLibraryManager();
    $webLibraryManager->addJsFile('public/js/jquery.validate.js');
    $webLibraryManager->addJsFile('public/js/validateForm.js');

    // Let's add the twig file to the template.
    $this->content->addHtmlElement(new TwigTemplate($this->twig, 'views/car/create.twig', array(
      "brands" => $brands,
    )));

    return new HtmlResponse($this->template);
  }
  /**
   * @URL("car/{id}/edit")

   * @Get
   */
  public function edit($id) {

    // Get car by id
    $car = $this->daoFactory->getCarDao()->getById($id)->jsonSerialize();

    // Get all brands
    $brands = $this->daoFactory->getBrandDao()->findAll()->jsonSerialize();

    // Add js files
    $webLibraryManager = \Mouf::getDefaultWebLibraryManager();
    $webLibraryManager->addJsFile('public/js/jquery.validate.js');
    $webLibraryManager->addJsFile('public/js/validateForm.js');

    // Let's add the twig file to the template.
    $this->content->addHtmlElement(new TwigTemplate($this->twig, 'views/car/edit.twig', array(
      "car" => $car,
      "brands" => $brands,
    )));

    return new HtmlResponse($this->template);
  }
  /**
   * @URL("car/{id}/delete")

   * @Get
   * @param int $id
   */
  public function delete($id) {

    $success_msg = '';
    $car = $this->daoFactory->getCarDao()->getById($id);

    if (!empty($car)) {
      $this->daoFactory->getCarDao()->delete($car);
      $success_msg = 'Content deleted successfully.';
    }

    // Resturn Updated content
    $cars = $this->daoFactory->getCarDao()->findAll()->jsonSerialize();

    // Let's add the twig file to the template.
    $content = new TwigTemplate($this->twig, 'views/car/car_list.twig', array(
      "success_msg" => $success_msg,
      "cars" => $cars,
    ));

    return new HtmlResponse($content); exit;
  }

  /**
   * @URL("car/save")

   * @Post
   * @param int $id
   * @param string $name
   * @param number $max_speed
   * @param int $brand
   */
  public function save($id = 0, $name, $max_speed, $brand) {

    // Fetch brand info
    $brand_obj = $this->daoFactory->getBrandDao()->getById($brand);
    
    // Update car
    if (!empty($id)) {
      $car = $this->daoFactory->getCarDao()->getById($id);
      $car->setName($name);
      $car->setMaxSpeed($max_speed);
      $car->setBrand($brand_obj);
    }
    // Create car
    else {
      $car = new CarBean($brand_obj, $name, $max_speed); 
    }
    
    // Save car info
    $this->daoFactory->getCarDao()->save($car);

    // Handle redirection
    return new RedirectResponse(ROOT_URL . 'cars');
  }

  /**
   * @URL("car/validateName")

   * @Post
   * @param int $id
   * @param string $name
   */
  public function validateName($id = 0, $name = '') {

    $status = TRUE;
    if (!empty($name)) {

      $filter = 'cars.name = :name';
      $params = array('name' => $name);

      // Set params if id is not empty
      if (!empty($id)) {
        $filter .= ' AND cars.id <> :id';
        $params['id'] = $id;
      }

      // Get car by name
      $cars = $this->daoFactory->getCarDao()->findByName($filter, $params)->jsonSerialize();

      if (count($cars) > 0) {
        $status = FALSE;
      }
    }
    return new JsonResponse([ "status"=> $status ]);
  }
}
