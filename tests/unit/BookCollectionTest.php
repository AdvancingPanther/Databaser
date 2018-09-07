<?php
//require_once('src/Model/DBModel.php');
require_once('./src/Model/DBModel.php');


class BookCollectionTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $dbModel;
    
    protected function _before()
    {
        $db = new PDO(
                'mysql:host=127.0.0.1;dbname=test;charset=utf8',
                'root',
                'LetscodePHP',
                array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
            );
        $this->dbModel = new DBModel($db);
    }

    protected function _after()
    {
    }

    // Test that all books are retrieved from the database
    public function testGetBookList()
    {
        
            $bookList = $this->dbModel->getBookList();
            $this->assertEquals(count($bookList), 3);
            $this->assertEquals($bookList[0]->id, 1);
            $this->assertEquals($bookList[0]->title, 'Jungle Book');
            $this->assertEquals($bookList[1]->id, 2);
            $this->assertEquals($bookList[1]->author, 'J. Walker');
            $this->assertEquals($bookList[2]->id, 3);
            $this->assertEquals($bookList[2]->description, 'Written by some smart gal.');
    
    }

    // Tests that information about a single book is retrieved from the database
    public function testGetBook()
    {
        $book = $this->dbModel->getBookById(1);

        // Sample tests of book list contents
        $this->assertEquals($book->id, 1);
        $this->assertEquals($book->title, 'Jungle Book');
        $this->assertEquals($book->author, 'R. Kipling');
        $this->assertEquals($book->description, 'A classic book.');
    }

    // Tests that get book operation fails if id is not numeric
    public function testGetBookRejected()                                                                   // *************
    {
        try {
            $this->dbModel->getBookById("1'; drop table book;--");
            $this->assertInstanceOf(InvalidArgumentException::class, null);
        } catch (InvalidArgumentException $e) {
        }
    }

    // Tests that a book can be successfully added and that the id was assigned. Four cases should be verified:
    //   1. title=>"New book", author=>"Some author", description=>"Some description" 
    //   2. title=>"New book", author=>"Some author", description=>""
      // 3. title=>"<script>document.body.style.visibility='hidden'</script>",
      //    author=>"<script>document.body.style.visibility='hidden'</script>",
      //    description=>"<script>document.body.style.visibility='hidden'</script>"
    public function testAddBook()
    {
        $testValues1 = ['title' => 'New book',
                       'author' => 'Some author',
                       'description' => 'Some description'];
        $testValues2 = ['title' => 'New book',
                       'author' => 'Some author',
                       'description' => ''];
        $testValues3 = ['title'=>"<script>document.body.style.visibility='hidden'</script>",
        'author'=>"<script>document.body.style.visibility='hidden'</script>",
        'description'=>"<script>document.body.style.visibility='hidden'</script>"];
     
        $loadTestValues = [1 => $testValues1, 2 => $testValues2, 3 => $testValues3 ];

        //  first i create new books that will include all test cases
        for ($i = 1; $i <= 3; $i++) 
        {
            $testValues = $loadTestValues[$i];
            $book = new Book($testValues['title'], $testValues['author'], $testValues['description']);
            $this->dbModel->addBook($book);
        }
        // and then check all of them for proper values
        for ($i = 1; $i <= 3; $i++) 
        {    
            $testValues = $loadTestValues[$i]; 
            // Id was successfully assigned
            $this->assertEquals(3+$i, $book->id = 3+$i);
            
            $this->tester->seeNumRecords(6, 'book');
            // Record was successfully inserted
            $this->tester->seeInDatabase('book', ['id' => 3+$i,
                                                  'title' => $testValues['title'],
                                                  'author' => $testValues['author'],
                                                  'description' => $testValues['description']]);
        }
        
        
    }

    // Tests that adding a book fails if id is not numeric
    public function testAddBookRejectedOnInvalidId()
    {
        $this->tester->expectException(Exception::class, function() {
            $this->dbModel->addBook("1'; drop table book;--");
        });
    }
 

    // Tests that adding a book fails mandatory fields are left blank
    public function testAddBookRejectedOnMandatoryFieldsMissing()
    {
        $testValues = ['id' => NULL,
                        'title' => NULL,
                       'author' => NULL,
                       'description' => NULL];
        $this->tester->expectException(Exception::class, function() {
            $this->dbModel->addBook($testValues);
        });
    }


    //Helper function to perform ModifyBook test on values that are listed inside. It uses the argument $i to 
    // load the coresponding test value and then runs the assert() and seeNum() and seeinDatabase() tests.
    protected function testModifyBookHelper($i) {
        $testValues1 = ['id' => 3, 'title' => 'New book',
                       'author' => 'Some author',
                       'description' => 'Some description'];
        $testValues2 = ['id' => 3, 'title' => 'New book',
                       'author' => 'Some author',
                       'description' => ''];
        $testValues3 = ['id' => 3, 'title'=>"<script>document.body.style.visibility='hidden'</script>",
        'author'=>"<script>document.body.style.visibility='hidden'</script>",
        'description'=>"<script>document.body.style.visibility='hidden'</script>"];
     
        $loadTestValues = [1 => $testValues1, 2 => $testValues2, 3 => $testValues3 ];

        $testValues = $loadTestValues[$i];
            $book = new Book( $testValues['title'], $testValues['author'], $testValues['description'], $testValues['id']);
            $this->dbModel->modifyBook($book);
            // Id was successfully assigned
            $this->assertEquals(3, $book->id);
        
            $this->tester->seeNumRecords(3, 'book');
            // Record was successfully inserted
            $this->tester->seeInDatabase('book', ['id' => 3,
                                                  'title' => $testValues['title'],
                                                  'author' => $testValues['author'],
                                                  'description' => $testValues['description']]);

    }
    // Tests that a book record can be successfully modified. Three cases should be verified:
    //   1. title=>"New book", author=>"Some author", description=>"Some description"
    //   2. title=>"New book", author=>"Some author", description=>""
    //   3. title=>"<script>document.body.style.visibility='hidden'</script>",
    //      author=>"<script>document.body.style.visibility='hidden'</script>",
    //      description=>"<script>document.body.style.visibility='hidden'</script>"
    public function testModifyBook1()
    {
        $this->testModifyBookHelper(1);
    }

    public function testModifyBook2()
    {
        $this->testModifyBookHelper(2);
    }

    public function testModifyBook3()
    {
        $this->testModifyBookHelper(3);
    }

    // Tests that modifying a book record fails if id is not numeric
    public function testModifyBookRejectedOnInvalidId()
    {       
        $this->tester->expectException(Exception::class, function() {
            $this->dbModel->modifyBook("1'; drop table book;--");
        });
    }
    
    // Tests that modifying a book record fails if mandatory fields are left blank
    // Tests that modifying a book record fails if mandatory fields are left blank
    public function testModifyBookRejectedOnMandatoryFieldsMissing()
    {       
          $testValues = ['title' => NULL,
                       'author' => NULL,
                       'description' => NULL];
        $this->tester->expectException(Exception::class, function() {
            $this->dbModel->modifyBook($testValues);
        });
    }
    
    // // Tests that a book record can be successfully deleted.
    public function testDeleteBook()
    {
        $this->dbModel->deleteBook(2);
        $this->tester->seeNumRecords(2, 'book');

        $book = $this->dbModel->getBookById(1);
        $this->assertEquals($book->id, 1);
        $this->assertEquals($book->title, 'Jungle Book');
        $this->assertEquals($book->author, 'R. Kipling');
        $this->assertEquals($book->description, 'A classic book.');

        $book = $this->dbModel->getBookById(3);
        $this->assertEquals($book->id, 3);
        $this->assertEquals($book->title, 'PHP & MySQL for Dummies');
        $this->assertEquals($book->author, 'J. Valade');
        $this->assertEquals($book->description, 'Written by some smart gal.');

    }
    
    // Tests that deleting book fails if id is not numeric
    public function testDeleteBookRejectedOnInvalidId()
    {
        try {
            $this->dbModel->deleteBook("1'; drop table book;--");
            $this->assertInstanceOf(InvalidArgumentException::class, null);
        } catch (InvalidArgumentException $e) {
        }
    }
}





