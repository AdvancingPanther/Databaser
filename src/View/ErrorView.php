<?php
/** The View of the IMT2571 Assignment #1 MVC-example that shows error information in case of failures.
 * @author Rune Hjelsvold
 * @see http://php-html.net/tutorials/model-view-controller-in-php/ The tutorial code used as basis.
 */

require_once('View.php');

/** The class that creates a page showing error information when the system fails in processing the request.
 * @author Rune Hjelsvold
 * @see http://php-html.net/tutorials/model-view-controller-in-php/ The tutorial code used as basis.
 */
class ErrorView extends View
{
    protected $message = null;
    
    /**
     * @param string $msg The message to pass to the user if set; a standard message will be
     *               passed otherwise.
     */
    public function __construct($msg = null)
    {
        if ($msg) {
            $this->message = $msg;
        } else {
            $this->message = 'Something bad happened.';
        }
    }

    /** Used by the superclass to generate page title
     */
    protected function getPageTitle()
    {
        return 'Error Page';
    }
    
    /** Used by the superclass to generate page content
     */
    protected function getPageContent()
    {
        return "<p>{$this->message}</p><p><a href=index.php>Back to book list</a></p>";
    }
}
