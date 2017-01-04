<?php
namespace MoufTest\Controllers;

use Mouf\Annotations\varAnnotation;
use Mouf\Html\Widgets\MessageService\Service\UserMessageInterface;
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

        // Get every car in the database
        $carlist = $this->daoFactory->getCarDao()->findAll();

        // Count pages
        $pageCount = ceil($carlist->count() / CARS_PER_PAGE);

        // Define current page
        if (isset($page)) {
            $currentPage = intval($page);

            if ($currentPage > $pageCount) {
                $currentPage = $pageCount;
            }
        } else {
            $currentPage = 1;
        }

        // Get the first entry (offset)
        $offset = ($currentPage - 1) * CARS_PER_PAGE;

        // Paginate
        $cars = $carlist->take($offset, CARS_PER_PAGE);

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
     * @URL("car/form/{id}")
     * @Get
     * @param int $id
     * @return HtmlResponse
     */
    public function form($id = null)
    {
        //var_dump(empty($id));
        $webLibraryManager = $this->template->getWebLibraryManager();
        $webLibraryManager->addJsFile('src/public/js/jquery.validate.min.js');
        $webLibraryManager->addJsFile('src/public/js/form-validation.js');

        $brands = $this->daoFactory->getBrandDao()->findAll();

        if (empty($id)) {
            $params = array(
                "brands" => $brands);
        } else {
            $car = $this->daoFactory->getCarDao()->getById($id);
            $params = array(
                "car" => $car,
                "brands" => $brands);
        }

        $this->content->addHtmlElement(new TwigTemplate($this->twig, 'views/car/form.twig', $params));

        return new HtmlResponse($this->template);
    }

    /**
     * @URL("car/update")
     * @Post
     * @param null $id
     * @param int $brand_id
     * @param string $name
     * @param int $max_speed
     * @return RedirectResponse
     */
    public function update($id = null, $brand_id, $name, $max_speed)
    {
        $brand = $this->daoFactory->getBrandDao()->getById($brand_id);

        if (empty($id)) {

            $carBean = new CarBean($brand, $name, $max_speed);

            $this->daoFactory->getCarDao()->save($carBean);

            set_user_message("La voiture a bien été ajoutée", UserMessageInterface::SUCCESS);

            return new RedirectResponse('http://localhost/mouf-tests/car/1');
        }

        $carBean = $this->daoFactory->getCarDao()->getById($id);

        $carBean->setBrand($brand);
        $carBean->setName($name);
        $carBean->setMaxSpeed($max_speed);

        $this->daoFactory->getCarDao()->save($carBean);

        set_user_message("La voiture a bien été modifiée", UserMessageInterface::SUCCESS);


        return new RedirectResponse('http://localhost/mouf-tests/car/form/' . $id);
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

        set_user_message("La voiture a bien été supprimée", UserMessageInterface::SUCCESS);
    }
}
