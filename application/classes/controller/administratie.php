<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Administratie extends Controller_Application
{
    /**
     * Initializes the controller, forcing all users to authenticate before touching anything
     */
    public function before()
    {
        parent::before();

        // Authenticate users
        $this->authenticate();
    }

    /**
     * List all past and current meals
     * @return void
     */
    public function action_index()
    {
        $this->template->content->upcoming_meals = ORM::factory('meal')->upcoming()->find_all();
        $this->template->content->previous_meals = ORM::factory('meal')->previous()->find_all();
    }

    /**
     * Creates a new meal
     * @return void
     */
    public function action_nieuwe_maaltijd()
    {
        $this->template->content->meal = $meal = ORM::factory('meal');

        if ($_POST) {
            $_POST = Helper_Form::prep_form($_POST);
            $meal->values($_POST, array('date','locked', 'event', 'promoted'));
            try {
                $meal->save();
                Flash::set(Flash::SUCCESS, 'Maaltijd toegevoegd');
                $this->request->redirect('/administratie');
            }
            catch (ORM_Validation_Exception $e) {
                // Nothing here, errors are retrieved in the view
                // specifically the Helper_Form class
            }
        }
    }

    /**
     * Edits a meal
     * @throws HTTP_Exception_404
     * @return void
     */
    public function action_bewerk()
    {
        $this->template->content->meal = $meal = ORM::factory('meal',$this->request->param('id'));
        if (! $meal->loaded()) {
            throw new HTTP_Exception_404;
        }

        if ($_POST) {
            $_POST = Helper_Form::prep_form($_POST);
            $_POST['promoted'] = (isset($_POST['promoted'])) ? (1) : (0);
            $meal->values($_POST, array('date','locked', 'event', 'promoted'));
            try {
                $meal->save();
                Flash::set(Flash::SUCCESS, 'Maaltijd geüpdate');
                $this->request->redirect(Route::url('default',array('controller' => 'administratie')));
            }
            catch (ORM_Validation_Exception $e) {
                // Nothing here, errors are retrieved in the view
            }
        }
    }

    /**
     * Removes a meal
     * @return void
     */
    public function action_verwijder()
    {
        $meal = ORM::factory('meal',$this->request->param('id'));
        $date = (string)$meal;

        $meal->delete();

        Flash::set(Flash::SUCCESS,"Maaltijd op $date verwijderd");
        $this->request->redirect('/administratie');
    }

    /**
     * Creates a registration
     * @return void
     */
    public function action_aanmelden()
    {
        // Build an array of the data to store
        $data = array(
            'meal_id' => (int)$_POST['meal_id'],
            'name' => (string)$_POST['name'],
            'handicap' => (string)$_POST['handicap']
        );
        // Find the meal we're changing
        $meal = ORM::factory('meal',$data['meal_id']);
        if (! $meal->loaded()) {
            throw new HTTP_Exception_404;
        }

        // Create a new registration
        $registration = ORM::factory('registration')->values($data,array('meal_id','name','handicap'));
        try {
            $registration->save();
            echo View::factory('administratie/_meal',array('meal' => $meal));
        }
        catch (ORM_Validation_Exception $e) {
            echo 'error';
        }
        //FIXME Manual override of template engine
        exit;
    }

    /**
     * Removes a registration
     * @return void
     */
    public function action_afmelden()
    {
        $registration = ORM::factory('registration',$this->request->param('id'));
        $name = $registration->name;
        $meal = $registration->meal;

        $registration->delete();

        if ($this->request->is_ajax()) {
            echo 'success';
            exit;
        }
        else {
            Flash::set(Flash::SUCCESS,"$name afgemeld voor de maaltijd op $meal");
            $this->request->redirect('/administratie');
        }
    }

    /**
     * Prints an array (json-encoded) of all upcoming dates with meals planned
     * used for the date-picker to hide all dates already filled
     * @return void
     */
    public function action_gevulde_dagen()
    {
        $id = Arr::get($_GET, 'meal_id');

        $meals = ORM::factory('meal')->upcoming()->find_all();
        $dates = array();
        foreach ($meals as $meal) {
            if ($id !== $meal->id) {
                $dates[] = $meal->date;
            }
        }
        header('Content-Type: application/json');
        print(json_encode($dates));
        exit;
    }
    
    /**
     * Prints a checklist for crossing off visiting users
     * not intended to be viewed, only printed
     */
    public function action_checklist()
    {
        $meal_id = $this->request->param('id');
        $meal = ORM::factory('meal',$meal_id);
        if (!$meal->loaded()) {
            throw new HTTP_Exception_404("Maaltijd niet gevonden");
        }
        echo View::factory('administratie/checklist',array('meal' => $meal));
        exit;
    }
}