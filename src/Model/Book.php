 <?php
/** The Book class is a part of the Model of the IMT2571 Assignment #1 MVC-example.
 * @author Rune Hjelsvold
 * @see http://php-html.net/tutorials/model-view-controller-in-php/ The tutorial code used as basis.
 */
 
/** The Model is the class holding data related to one book.
 */
class Book
{
    
    /**
      * @var int The database id of the book; -1 for books not yet stored in database.
      */
    public $id;
    
    /**
      * @var string The title of the book.
      */
    public $title;
    
    /**
      * @var int The author of the book.
      */
    public $author;
    
    /**
      * @var int The description of the book.
      */
    public $description;

    /** Constructor
     * @param string $title Book title
     * @param string $author Book author
     * @param string $description Book description
     * @param integer $id Book id (optional)
     */
    public function __construct($title, $author, $description, $id = -1)
    {
        $this->id = $id;
        $this->title = $title;
        $this->author = $author;
        $this->description = $description;
    }
}
