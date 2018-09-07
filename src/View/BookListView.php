<?php
/** The View of the IMT2571 Assignment #1 MVC-example that shows the complete collection of books.
 * @author Rune Hjelsvold
 * @see http://php-html.net/tutorials/model-view-controller-in-php/ The tutorial code used as basis.
 */
 
require_once('Model/Book.php');
require_once('View.php');

/** The BooklistView is the class that creates the page showing the complete collection of books.
 */
class BookListView extends View
{
    /** The list of books in the collection.
     * @var Book[]
     */
    protected $books;
    
    /** The book that was inserted if the page is a response to and add operation.
     * @var Book
     */
    protected $newBook;
    
    /** The name of the operation parameter to be included on the page.
     * @var string
     */
    protected $opParamName;
    
    /** The add operation parameter value to be used on the page.
     * @var string
     */
    protected $addOpName;
    
    /** Constructor
     * @param Book[] $books The collection of books - in the form of an array of Books - to be shown.
     * @param string $opParamName The name of the parameter to used in the query string for passing the
     *                            operation to be performed.
     * @param string $addOpName The name to be used for the add operation.
     * @param Book $newBook The attribute should be set to null for all operations except for add operations.
     *                      In the case of an addOperation, the book that was added to the collection should
     *                      be passed.
     */
    public function __construct($books, $opParamName, $addOpName, $newBook = null)
    {
        $this->books = $books;
        $this->newBook = $newBook;
        $this->opParamName = $opParamName;
        $this->addOpName = $addOpName;
    }
    
    /** Used by the superclass to generate page title
     * @return string Page title to be generated.
     */
    protected function getPageTitle()
    {
        return 'Book Collection';
    }
    
    /** Used by the superclass to generate page content
     * @return string Content of page to be generated.
     */
    protected function getPageContent()
    {
        if ($this->newBook) {
            $content = <<<HTML
<h2>Book Successfully Added</h2>
<p id='newBook'>
HTML;
            $content .= 'The book, ' . $this->newBook->title . ', written by ' . $this->newBook->author
                      . ' was successfully added to the collection and was assigned ID: <span id="newBookId">'
                      . $this->newBook->id . '</span>.</p>';
        } else {
            $content = '';
        }
        
        $content .= <<<HTML
<h2>Current Titles</h2>
<table id='bookList'>
  <thead>
    <tr><th>ID</th><th>Title</th><th>Author</th><th>Description</th></tr>
  </thead>
  <tbody>
HTML;
        if (isset($this->books)) {
            foreach ($this->books as $book) {
                $content .= '<tr id="book' . $book->id . '">'
                          . '<td><a href="index.php?id=' . $book->id . '">' . $book->id . '</a></td>'
                          . '<td>' . htmlspecialchars($book->title) . '</td>'
                          . '<td>' . htmlspecialchars($book->author) . '</td>'
                          . '<td>' . htmlspecialchars($book->description) . '</td></tr>';
            }
        }

        $content .= <<<HTML
  </tbody>
</table>
<h2>New Titles</h2>
HTML;
        $content .= $this->createAddForm();

        return $content;
    }
    
    /** Helper function generating HTML code for the form for adding new books to the collection
     * @return string The HTML code to be generated.
     */
    protected function createAddForm()
    {
        return
        '<form id="addForm" action="index.php" method="post">'
        . '<input name="'.$this->opParamName.'" value="'.$this->addOpName.'" type="hidden"/>'
        . '<label for="title">Title:</label>'
        . '<input name="title" type="text" value=""/>'
        . '<label for="author">Author:</label>'
        . '<input name="author" type="text" value=""/>'
        . '<label for="description">Description:</label>'
        . '<input name="description" type="text" value=""/>'
        . '<input type="submit" value="Add new book"/>'
        . '</form>';
    }
}
