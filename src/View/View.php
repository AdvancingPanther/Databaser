<?php
/** The abstract class constituting the top of the View of the IMT2571 Assignment #1 MVC-example.
 * @author Rune Hjelsvold
 * @see http://php-html.net/tutorials/model-view-controller-in-php/ The tutorial code used as basis.
 */
 
/** The View is the superclass that sets up the page for each of the views.
 */
abstract class View
{
    /** Is used to retrieve the title of the given view page
     * @return string View page title.
     */
    abstract protected function getPageTitle();
    
    /** Is used to retrieve the page content of the given view page
     * @return string View page content.
     */
    abstract protected function getPageContent();

    /** Creates HTML code for the given view page
     */
    public function create()
    {
        echo <<<HTML
<!DOCTYPE html>
<html>
<head>
<title>
HTML;
        echo $this->getPageTitle();
        echo <<<HTML
</title>
<style>
h2 {
    margin-bottom: 1ex;
}

.decimal {
    text-align: right;
}

table {
    border-collapse: collapse;
}

table, th, td {
    border: 1px solid black;
    padding-right: 1ex;
    padding-left: 1ex;
}

input, select, label {
    display: block;
}

input, select {
    margin-top: 0.5ex;
}

input.addButton {
    margin-top: 2ex;
}

label {
    margin-top: 1ex;
    font-weight: bold;
}
</style></head>
<body>
<h1 id="pageTitle">
HTML;
        echo $this->getPageTitle();
        echo <<<HTML
</h1>
HTML;
        echo $this->getPageContent();
        echo <<<HTML
</body>
</html>    
HTML;
    }
}
