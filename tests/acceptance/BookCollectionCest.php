<?php
use Codeception\Util\Locator;

class BookCollectionCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    // Test to verify that the booklist is displayed as expected
    public function showBookListTest(AcceptanceTester $I)
    {
        $I->amOnPage('index.php');
        
        // Book list content
        $I->seeInTitle('Book Collection');
        $I->seeNumberOfElements('table#bookList>tbody>tr', 3);
        // Check sample book values
        $I->see('Jungle Book', 'tr#book1>td:nth-child(2)');
        $I->see('J. Walker', 'tr#book2>td:nth-child(3)');
        $I->see('Written by some smart gal.', 'tr#book3>td:nth-child(4)');
        $I->seeElement('tr#book1>td:first-child>a', ['href' => 'index.php?id=1']);
        $I->seeElement('tr#book2>td:first-child>a', ['href' => 'index.php?id=2']);
        $I->seeElement('tr#book3>td:first-child>a', ['href' => 'index.php?id=3']);
        
        // Add new book form content
        $I->seeElement('form#addForm>input', ['name' => 'title']);
        $I->seeElement('form#addForm>input', ['name' => 'author']);
        $I->seeElement('form#addForm>input', ['name' => 'description']);
        $I->seeElement('form#addForm>input', ['type' => 'submit',
                                              'value' => 'Add new book']);
    }
    
    // Test to verify that the book details page is displayed as expected
    public function showBookDetailsTest(AcceptanceTester $I)
    {
        $I->amOnPage('index.php');
        $I->click(1);
        $this->verifyBookDetails($I, 'Jungle Book', 'R. Kipling', 'A classic book.');
        $I->seeLink('Back to book list','index');
        
        // Buttons for updating and deleting book information
        $I->seeElement('form#modForm>input', ['type' => 'submit',
                                              'value' => 'Update book record']);
        $I->seeElement('form#delForm>input', ['type' => 'submit',
                                              'value' => 'Delete book record']);        
    }
    
    // Test to verify that non-numeric book id's are rejected when requesting book information
    public function invalidBookIdRejectedTest(AcceptanceTester $I)
    {
        $I->amOnPage("index.php?id=1'; drop table book;--");
        $I->seeInTitle('Error Page');        
    }
    
    // Helper function that verifies that the book information on the current page matches the parameter values
    protected function verifyBookDetails(AcceptanceTester $I, String $title, String $author, String $description)
    {
        $I->seeInTitle('Book Details');
        $I->seeElement('form#modForm>input', ['name' => 'title',
                                              'value' => $title]);
        $I->seeElement('form#modForm>input', ['name' => 'author',
                                              'value' => $author]);
        $I->seeElement('form#modForm>input', ['name' => 'description',
                                              'value' => $description]);
    }

    protected function verifyBookDetailsMod(AcceptanceTester $I, String $title, String $author, 
        String $description, $ID )
    {
        $I->amOnPage('index.php');

        $I->submitForm('#addForm', ['title' => $title, 
                                    'author' => $author,
                                    'description' => $description]);

        // Getting booklist with new book added as ID:4
        $I->seeInTitle('Book Collection');
        $I->seeNumberOfElements('table#bookList>tbody>tr', $ID);
        $I->see('ID: ' . $ID);
        $I->seeElement('tr#book' . $ID . '>td:first-child>a', ['href' => 'index.php?id=' . $ID]);
        $I->see ($title, 'tr#book' . $ID . '>td:nth-child(2)');
        $I->see($author, 'tr#book' . $ID . '>td:nth-child(3)');
        $I->see($description, 'tr#book' . $ID . '>td:nth-child(4)');
        $I->seeLink($ID,'index.php?id=' . $ID);
    }
   
    // Test to verify that new books can be added. Four cases should be verified:
    //   1. title=>"New book", author=>"Some author", description=>"Some description"
    //   2. title=>"New book", author=>"Some author", description=>""
    //   3. title=>"A Girl's memoirs", author=>"Jean d'Arc", description=>"Single quotes (') should not break anything"
    //   4. title=>"<script>document.body.style.visibility='hidden'</script>",
    //      author=>"<script>document.body.style.visibility='hidden'</script>",
    //      description=>"<script>document.body.style.visibility='hidden'</script>"
   
    public function successfulAddBookTest(AcceptanceTester $I)
    {
        $testValues4 = ['title'=>"<script>document.body.style.visibility='hidden'</script>",
         'author'=>"<script>document.body.style.visibility='hidden'</script>",
         'description'=>"<script>document.body.style.visibility='hidden'</script>"];
        $testValues3 = ['title'=>"A Girl's memoirs", 'author'=>"Jean d'Arc", 'description'=>"Single quotes (') should not break anything"];
        $testValues2 = ['title'=>"New book", 'author'=>"Some author", 'description'=>""];
        $testValues1 = ['title' => 'New book',
                       'author' => 'Some author',
                       'description' => 'Some description'];
        $loadTestValues = [1 => $testValues1, 2 => $testValues2, 3 => $testValues3, 4 => $testValues4];

        // running all testValues through for-loop with a help of a helper function verifBookDetailsMod
        // the last argument $ID has to be 3 + $i beause it all starts from raw 4 and onwards (can not pass
        // $ID = 1, must start from 4 and be scalable)
        for ($i = 1; $i <= 4; $i++) 
        {
            $this->verifyBookDetailsMod($I, $loadTestValues[$i]['title'], 
                $loadTestValues[$i]['author'], 
                $loadTestValues[$i]['description'], 3 + $i);
        }
    }


        
    // Test to verify that adding a book fails if mandatory fields are missing
    public function addBookWithoutMandatoryFieldsTest(AcceptanceTester $I)
    {
        $I->amOnPage('index.php');  
        $testValues = ['id' => NULL, 'title' => NULL,
                       'author' => NULL,
                       'description' => NULL];
       
        $I->submitForm('#addForm', ['id' => $testValues['id'], 'title' => $testValues['title'], 
                                    'author' => $testValues['author'],
                                    'description' => $testValues['description']]);

        // I see error, addition failed
        $I->seeInTitle('Error Page');     
    }
    
    // Test to verify that book records can be modified successfully. Four cases should be verified:
    //   1. title=>"Different title", author=>"Different Author", description=>"Different description"
    //   2. title=>"Different title", author=>"Different Author", description=>""
    //   3. title=>"A Girl's memoirs", author=>"Jean d'Arc", description=>"Single quotes (') should not break anything"
    //   4. title=>"<script>document.body.style.visibility='hidden'</script>",
    //      author=>"<script>document.body.style.visibility='hidden'</script>",
    //      description=>"<script>document.body.style.visibility='hidden'</script>"
    public function successfulModifyBookTest(AcceptanceTester $I)
    {
        $I->amOnPage('index.php?id=2'); 
        $testValues4 = ['title'=>"<script>document.body.style.visibility='hidden'</script>",
         'author'=>"<script>document.body.style.visibility='hidden'</script>",
         'description'=>"<script>document.body.style.visibility='hidden'</script>"];
        $testValues3 = ['title'=>"A Girl's memoirs", 'author'=>"Jean d'Arc", 'description'=>"Single quotes (') should not break anything"];
        $testValues2 = ['title'=>"New book", 'author'=>"Some author", 'description'=>""];
        $testValues1 = ['title' => "Different title",
                       'author' => "Different Author",
                       'description' => "Different description"];
        $loadTestValues = [1 => $testValues1, 2 => $testValues2, 3 => $testValues3, 4 => $testValues4];

        for ($i = 1; $i <= 4; $i++) {
            $I->amOnPage('index.php?id=2'); 
            $testValues = $loadTestValues[$i];
            $I->submitForm('#modForm', ['title' => $testValues['title'], 
                                        'author' => $testValues['author'],
                                        'description' => $testValues['description']]);

            // Getting booklist with  book moddified as ID:2
            $I->amOnPage('index.php'); 
            $I->seeInTitle('Book Collection');
            $I->seeNumberOfElements('table#bookList>tbody>tr', 3);
            $I->seeLink('2','index.php?id=2');
            //$I->see('ID: 2');
            $I->seeElement('tr#book2>td:first-child>a', ['href' => 'index.php?id=2']);
            $I->see($testValues['title'], 'tr#book2>td:nth-child(2)');
            $I->see($testValues['author'], 'tr#book2>td:nth-child(3)');
            $I->see($testValues['description'], 'tr#book2>td:nth-child(4)');
            $I->seeLink('2','index.php?id=2');
        }

    }
    
    // Test to verify that modifying a book fails if mandatory fields are missing
    public function modifyBookWithoutMandatoryFieldsTest(AcceptanceTester $I)
    {
        $testValues1 = ['title' => NULL,
                       'author' => NULL,
                       'description' => NULL];
        $testValues2 = ['title' => NULL,
                       'author' => 'LOL',
                       'description' => NULL];
        $testValues3 = ['title' => 'LOL',
                       'author' => NULL,
                       'description' => NULL];
        $loadTestValues = [1 => $testValues1, 2 => $testValues2, 3 => $testValues3];

        // Now i will loop through array of test values $loadTestValues and run all 3 cases
        for ($i = 1; $i <= 3; $i ++)
            {
            $testValues = $loadTestValues[$i];
            $I->amOnPage('index.php?id=2');
            $I->submitForm('#modForm', ['title' => $testValues['title'], 
                                        'author' => $testValues['author'],
                                        'description' => $testValues['description']]);

            // I see error, addition failed
            $I->seeInTitle('Error Page'); 
            }          
    }
    
    // Test to verify that deleting a book succeeds.
    public function successfulDeleteBookTest(AcceptanceTester $I)
    {
        $I->amOnPage('index.php?id=2');
        
        $I->seeElement('form#delForm>input', ['type' => 'submit',
                                              'value' => 'Delete book record']); 
        $I->click('Delete book record');
         
        $I->seeNumberOfElements('table#bookList>tbody>tr', 2);
        $I->dontSeeElement('tr#book2');
    }
    
    // Test to verify that deleting a book with non-id fails
    public function deleteBookWithInvalidIdTest(AcceptanceTester $I)
    { 
        $I->amOnPage("index.php?id=2'; drop table book;--");
        $I->seeInTitle('Error Page');       
    }
}