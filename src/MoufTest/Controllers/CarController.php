<?php
namespace MoufTest\Controllers;

use Mouf\Html\Renderer\Twig\TwigTemplate;
use Mouf\Mvc\Splash\Annotations\Get;
use Mouf\Mvc\Splash\Annotations\Post;
use Mouf\Mvc\Splash\Annotations\Put;
use Mouf\Mvc\Splash\Annotations\Delete;
use Mouf\Mvc\Splash\Annotations\URL;
use Mouf\Html\Template\TemplateInterface;
use Mouf\Html\HtmlElement\HtmlBlock;
use Mouf\Mvc\Splash\HtmlResponse;
use MoufTest\Model\Bean\CarBean;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use MoufTest\Model\Dao\Generated\DaoFactory;
use Twig_Environment;
use Zend\Diactoros\Response\JsonResponse;

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
     * @return \Mouf\Html\Utils\WebLibraryManager\WebLibraryManager
     */
    public function getLibraries(){
        $webLibraryManager = $this->template->getWebLibraryManager();

        $webLibraryManager->addCssFile('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');
        $webLibraryManager->addCssFile('public/css/main.css');

        $webLibraryManager->addJsFile('https://ajax.googleapis.com/ajax/libs/angularjs/1.6.2/angular.min.js');
        $webLibraryManager->addJsFile('https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular-messages.min.js');
        $webLibraryManager->addJsFile('https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.6.2/angular-route.js');
        $webLibraryManager->addJsFile('https://cdnjs.cloudflare.com/ajax/libs/angular-ui-bootstrap/2.5.0/ui-bootstrap-tpls.min.js');

        $webLibraryManager->addJsFile('public/js/app.js');
        $webLibraryManager->addJsFile('public/js/controllers/listCars.js');
        $webLibraryManager->addJsFile('public/js/controllers/addCar.js');
        $webLibraryManager->addJsFile('public/js/controllers/updateCar.js');
        $webLibraryManager->addJsFile('public/js/directives/uniqueModel.js');

        $webLibraryManager->addJsFile('public/js/factory/carsService.js');
        $webLibraryManager->addJsFile('public/js/factory/isModelAvailable.js');

        return $webLibraryManager;
    }
    /**
     * @URL("/api/cars")
     * @Get
     * @return JsonResponse
     */
    public function apiGetCars() {

        $cars = $this->daoFactory->getCarDao()->findAll();

        return new JsonResponse([ "status"=>"ok", "cars" => $cars ]);
    }

    /**
     * @URL("/api/brands")
     * @Get
     * @return JsonResponse
     */
    public function apiGetBrands() {

        $brands = $this->daoFactory->getBrandDao()->findAll();

        return new JsonResponse([ "status"=>"ok", "brands" => $brands ]);
    }

    /**
     * @URL("/api/car/{id}")
     * @Get
     * @param integer $id
     * @return JsonResponse
     */
    public function apiGetCar($id) {
        // TODO: write content of action here

        $car = $this->daoFactory->getCarDao()->getById($id);

        return new JsonResponse([ "status"=>"ok", "car" => $car ]);
    }

    /**
     * @URL("/api/cars")
     * @Post
     * @param ServerRequestInterface $request
     * @return JsonResponse
     */
    public function apiPostCar(ServerRequestInterface $request){

        $brandId = $request->getParsedBody()['brand']['id'];
        $name = $request->getParsedBody()['car']['name'];
        $maxSpeed = $request->getParsedBody()['car']['maxSpeed'];

        $brand = $this->daoFactory->getBrandDao()->getById($brandId);

        $newCar = new CarBean($brand, $name, $maxSpeed);

        $this->daoFactory->getCarDao()->save($newCar);

        return new JsonResponse([ "status"=>"ok", "newCar" => $newCar ]);

    }

    /**
     * @URL("/api/car/{id}")
     * @Put
     * @param ServerRequestInterface $request
     * @param integer $id
     * @return JsonResponse
     */
    public function apiPutCar($id, ServerRequestInterface $request){

        $brandId = $request->getParsedBody()['brand']['id'];
        $name = $request->getParsedBody()['car']['name'];
        $maxSpeed = $request->getParsedBody()['car']['maxSpeed'];

        $car = $this->daoFactory->getCarDao()->getById($id);

        $brand = $this->daoFactory->getBrandDao()->getById($brandId);

        $car->setName($name);
        $car->setMaxSpeed($maxSpeed);
        $car->setBrand($brand);

        $this->daoFactory->getCarDao()->save($car);

        return new JsonResponse([ "status"=>"ok", "updateCar" => $car ]);

    }

    /**
     * @URL("/api/car/{id}")
     * @Delete
     * @param integer $id
     * @return JsonResponse
     */
    public function apiDeleteCar($id){

        $car = $this->daoFactory->getCarDao()->getById($id);

        $this->daoFactory->getCarDao()->delete($car);

        return new JsonResponse([ "status"=>"ok", "deleteCar" => $car ]);

    }

    /**
     * @URL("/api/{model}")
     * @Get
     * @param string $model
     * @return JsonResponse
     */
    public function apiGetModel($model){

        $request = $this->daoFactory->getCarDao()->getCarByModel($model);

        return new JsonResponse([ "status"=>"ok", "modelExist" => $request ]);

    }

    /**
     * @URL("/cars")
     * @Get
     * @return HtmlResponse
     */
    public function getCars() {

        $this->getLibraries();

        $this->content->addHtmlElement(new TwigTemplate($this->twig, 'views/cars/list.twig', array()));

        return new HtmlResponse($this->template);
    }

    /**
     * @URL("/car")
     * @Get
     * @return HtmlResponse
     */
    public function postCar() {
        // TODO: write content of action here

        $this->getLibraries();

        $this->content->addHtmlElement(new TwigTemplate($this->twig, 'views/cars/add.twig', array()));

        return new HtmlResponse($this->template);
    }

    /**
     * @URL("/car/{id}")
     * @Get
     * HtmlResponse
     */
    public function updateCar() {
        // TODO: write content of action here

        $this->getLibraries();

        $this->content->addHtmlElement(new TwigTemplate($this->twig, 'views/cars/edit.twig', array()));

        return new HtmlResponse($this->template);
    }

}
