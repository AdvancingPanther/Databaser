<?php
/** The Controller of the IMT2571 Assignment #1 MVC-example.
 * @author Rune Hjelsvold
 * @see http://php-html.net/tutorials/model-view-controller-in-php/ The tutorial code used as basis.
 */
// Needed to overcome a Chrome bug when testing for html type input
header('X-XSS-Protection: 0');

// require_once('Model/Model.php');
require_once('Model/DBModel.php');
require_once('Model/Book.php');
require_once('View/BookListView.php');
require_once('View/BookView.php');
require_once('View/ErrorView.php');

/** The Controller is responsible for handling user requests, for exchanging data with the Model,
 * and for passing user response data to the various Views.
 * @see Model The Model class holding book data.
 * @see BookView The View class displaying information about one book.
 * @see BookListView The View class displaying information about all books.
 * @see ErrorView The View class displaying information about errors encountered when processing the request.
 */
class Controller
{
    public $model;
    
    /**
     * Query string key passed in HTTP for identifying the requested operation.
     */
    const OP_PARAM_NAME = 'op';
    
    /**
     * Query string value passed in operation for delete operations.
     * @see OP_PARAM_NAME
     */
    const DEL_OP_NAME = 'del';
    
    /**
     * Query string value passed in operation for insert operations.
     * @see OP_PARAM_NAME
     */
    const ADD_OP_NAME = 'add';
    
    /**
     * Query string value passed in operation for modification operations.
     * @see OP_PARAM_NAME
     */
    const MOD_OP_NAME = 'mod';
    
    public function __construct()
    {
        session_start();
        //$this->model = new Model();
       $this->model = new DBModel();
       // $this->model = new Model();
    }
    
    /** The one function running the controller code.
     */
    public function invoke()
    {
        try {
            if (isset($_GET['id'])) {
                // A specific book is selected - show the requested book
                $book = $this->model->getBookById($_GET['id']);
                if ($book) {
                    $view = new BookView(
                        $book,
                        self::OP_PARAM_NAME,
                        self::DEL_OP_NAME,
                        self::MOD_OP_NAME
                    );
                    $view->create();
                } else {
                    $view = new ErrorView();
                    $view->create();
                }
            } else {
                // Variable used to pass newly added books to the BookListView
                $newBook = null;
                //A book record is to be added, deleted, or modified
                if (isset($_POST[self::OP_PARAM_NAME])) {
                    switch ($_POST[self::OP_PARAM_NAME]) {
                    case self::ADD_OP_NAME:
                        $newBook = new Book(
                            $_POST['title'],
                            $_POST['author'],
                            $_POST['description']
                        );
                        $this->model->addBook($newBook);
                        break;
                    case self::DEL_OP_NAME:
                        $this->model->deleteBook($_POST['id']);
                        break;
                    case self::MOD_OP_NAME:
                        $book = new Book(
                            $_POST['title'],
                            $_POST['author'],
                            $_POST['description'],
                            $_POST['id']
                        );
                        $this->model->modifyBook($book);
                        break;
                    }
                }
                // no special book is requested, we'll show a list of all available books
                $books = $this->model->getBookList();
                $view = new BookListView(
                    $books,
                    self::OP_PARAM_NAME,
                    self::ADD_OP_NAME,
                    $newBook
                );
                $view->create();
            }
        } catch (InvalidArgumentException $e) {
            // User entered invalid data
            $view = new ErrorView('Invalid data received - please add valid book data');
            $view->create();
        } catch (PDOException $e) {
            // Database operation failed
            $view = new ErrorView('Database operation failed - please try again later');
            $view->create();
        } catch (Exception $e) {
            // Something else failed
            $view = new ErrorView();
            $view->create();
        }
    }
}
