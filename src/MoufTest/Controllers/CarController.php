<?php
namespace MoufTest\Controllers;

use Mouf\Mvc\Splash\Annotations\Get;
use Mouf\Mvc\Splash\Annotations\Post;
use Mouf\Mvc\Splash\Annotations\Put;
use Mouf\Mvc\Splash\Annotations\Delete;
use Mouf\Mvc\Splash\Annotations\URL;
use Mouf\Html\Template\TemplateInterface;
use Mouf\Html\HtmlElement\HtmlBlock;
use MoufTest\Model\Bean\BrandBean;
use MoufTest\Model\Bean\CarBean;
use MoufTest\Model\Dao\BrandDao;
use Psr\Log\LoggerInterface;
use MoufTest\Model\Dao\Generated\DaoFactory;
use \Twig_Environment;
use Mouf\Html\Renderer\Twig\TwigTemplate;
use Zend\Diactoros\Response\RedirectResponse;
use Mouf\Mvc\Splash\HtmlResponse;

/**
 * TODO: write controller comment
 */
class CarController
{

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
    public function __construct(LoggerInterface $logger, TemplateInterface $template, HtmlBlock $content, DaoFactory $daoFactory, Twig_Environment $twig)
    {
        $this->logger = $logger;
        $this->template = $template;
        $this->content = $content;
        $this->daoFactory = $daoFactory;
        $this->twig = $twig;
    }

    /**
     * @URL("car/{page}")
     * @Get
     * @param int $page
     * @return HtmlResponse
     */
    public function index($page = 1)
    {
        $webLibraryManager = $this->template->getWebLibraryManager();
        $webLibraryManager->addJsFile('src/public/js/jquery.tablesorter.min.js');
        $webLibraryManager->addJsFile('src/public/js/livesearch.min.js');

        // Define cars showed by page
        $cars_per_page = 4;

        // Get every car in the database
        $carlist = $this->daoFactory->getCarDao()->findAll();

        // Count pages
        $pageCount = ceil($carlist->count() / $cars_per_page);

        // Define current page
        // TODO try to use getCurrentPage
        if (isset($page)) {
            $currentPage = intval($page);

            if ($currentPage > $pageCount) {
                $currentPage = $pageCount;
            }
        } else {
            $currentPage = 1;
        }

        // Get the first entry (offset)
        // TODO try to use first()
        $offset = ($currentPage - 1) * $cars_per_page;

        // Paginate
        $cars = $carlist->take($offset, $cars_per_page);

        $this->content->addHtmlElement(new TwigTemplate($this->twig, 'views/car/index.twig',
            array(
                "cars" => $cars,
                "pageCount" => $pageCount,
                "current" => $cars->getCurrentPage()
            )
        ));

        return new HtmlResponse($this->template);
    }

    /**
     * @URL("car/create")
     * @Get
     */
    public function create()
    {
        $webLibraryManager = $this->template->getWebLibraryManager();
        $webLibraryManager->addJsFile('src/public/js/jquery.validate.min.js');
        $webLibraryManager->addJsFile('src/public/js/form-validation.js');

        $brands = $this->daoFactory->getBrandDao()->findAll()->jsonSerialize();

        $this->content->addHtmlElement(new TwigTemplate($this->twig, 'views/car/create.twig', array('brands' => $brands)));

        return new HtmlResponse($this->template);
    }

    /**
     * @URL("car/store")
     * @Post
     * @param int $brand_id
     * @param string $name
     * @param int $max_speed
     * @return RedirectResponse
     */
    public function store($brand_id, $name, $max_speed)
    {


        $brand = $this->daoFactory->getBrandDao()->getById($brand_id);
        $carBean = new CarBean($brand, $name, $max_speed);
        $this->daoFactory->getCarDao()->save($carBean);

        Return new RedirectResponse('http://localhost/mouf-tests/car/1');
    }

    /**
     * @URL("car/{id}/edit")
     * @Get
     * @param int $id
     * @return HtmlResponse
     */
    public function edit($id)
    {
        $brands = $this->daoFactory->getBrandDao()->findAll()->jsonSerialize();

        $car = $this->daoFactory->getCarDao()->getById($id);

        // Let's add the twig file to the template.
        $this->content->addHtmlElement(new TwigTemplate($this->twig, 'views/car/edit.twig', array(
            "car" => $car,
            "brands" => $brands)));

        return new HtmlResponse($this->template);
    }

    /**
     * @URL("car/update")
     * @Post
     * @param int $id
     * @param int $brand_id
     * @param string $name
     * @param int $max_speed
     * @return RedirectResponse
     */
    public function update($id, $brand_id, $name, $max_speed)
    {
        $brand = $this->daoFactory->getBrandDao()->getById($brand_id);

        $carBean = $this->daoFactory->getCarDao()->getById($id);

        $carBean->setBrand($brand);
        $carBean->setName($name);
        $carBean->setMaxSpeed($max_speed);

        $this->daoFactory->getCarDao()->save($carBean);

        Return new RedirectResponse('http://localhost/mouf-tests/car/1');
    }

    /**
     * @URL("car/{id}/delete")
     * @Delete
     * @param int $id
     */
    public function delete($id)
    {
        $brand = $this->daoFactory->getCarDao()->getById($id);
        $this->daoFactory->getCarDao()->delete($brand);
    }
}
