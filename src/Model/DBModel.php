<?php
/** The Model implementation of the IMT2571 Assignment #1 MVC-example, storing data in a MySQL database using PDO.
 * @author Rune Hjelsvold
 * @see http://php-html.net/tutorials/model-view-controller-in-php/ The tutorial code used as basis.
 */

require_once("AbstractModel.php");
require_once("Book.php");

/** The Model is the class holding data about a collection of books.
 * @todo implement class functionality.
 */
class DBModel extends AbstractModel
{
    protected $db = null;
    
    /**
     * @param PDO $db PDO object for the database; a new one will be created if no PDO object
     *                is passed
     * @todo Implement function using PDO and a real database.
     * @throws PDOException
     */
    public function __construct($db = null)
    {
        if ($db) {
            $this->db = $db;
        } else {
            // Create PDO connection
            $this->db = new PDO('mysql:host=localhost;dbname=test;charset=utf8', 'root','LetscodePHP', array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
            } 
        
    }

    /** Function returning the complete list of books in the collection. Books are
     * returned in order of id.
     * @return Book[] An array of book objects indexed and ordered by their id.
     * @todo Implement function using PDO and a real database.
     * @throws PDOException
     */
    public function getBookList()
    {   
        //  
            $booklist = array();
            $stmt = $this->db->query('SELECT * FROM book ORDER BY id');
            $booklist = $stmt->fetchAll(PDO::FETCH_OBJ);
            return $booklist;
    }
    
    /** Function retrieving information about a given book in the collection.
     * @param integer $id the id of the book to be retrieved
     * @return Book|null The book matching the $id exists in the collection; null otherwise.
     * @todo Implement function using PDO and a real database.
     * @throws PDOException
     */
    public function getBookById($id)
    {   
        
        DBModel::verifyId($id);                 // tests if passes id is an actual integer type
        $book = null;
        $stmt = $this->db->prepare('SELECT * FROM book WHERE id = ? ');
        $stmt->execute([$id]);
        $book = $stmt->fetch(PDO::FETCH_OBJ);
        return $book;
        
    }
    
    /** Adds a new book to the collection.
     * @param Book $book The book to be added - the id of the book will be set after successful insertion.
     * @todo Implement function using PDO and a real database.
     * @throws PDOException
     */
    public function addBook($book)
    {  
        DBModel::verifyBook($book); 
        DBModel::verifyId($book->id);
        $stmt = $this->db->prepare('INSERT INTO book VALUES ( :id, :title, :author, :description)');
        $stmt->bindValue(':id', NULL);
        $stmt->bindValue(':title', $book->title);
        $stmt->bindValue(':author', $book->author);
        $stmt->bindValue(':description', $book->description);
        $stmt->execute();
        $book->id = $this->db->lastInsertId();
        echo ("New book id: " . $book->id . "<br>");
        
    }


    /** Modifies data related to a book in the collection.
     * @param Book $book The book data to be kept.
     * @todo Implement function using PDO and a real database.
     * @throws PDOException
    */
    public function modifyBook($book)
    {
        DBModel::verifyBook($book); 
        DBModel::verifyId($book->id);
        $stmt = $this->db->prepare('UPDATE book SET title = :title, author = :author, 
        description = :description WHERE id = :id');
        $stmt->execute(['title' => $book->title, 'author' => $book->author, 
        'description' => $book->description, 'id' => $book->id]);
        echo ("Book updated" . "<br>");
        
    }

    /** Deletes data related to a book from the collection.
     * @param $id integer The id of the book that should be removed from the collection.
     * @todo Implement function using PDO and a real database.
     * @throws PDOException
    */
    public function deleteBook($id)
    {   
        DBModel::verifyId($id);         // tests if passes id is an actual integer type
        $stmt = $this->db->prepare('DELETE FROM book WHERE id = :id');
        $stmt->execute(['id' => $id]);
        echo ("Book deleted" . "<br>");    
    }
}
