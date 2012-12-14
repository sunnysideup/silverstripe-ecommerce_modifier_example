<?php

/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package: ecommerce
 * @sub-package: examples
 * @description: This is an example modifier that developers can use
 * as a starting point for writing their own modifiers.
 *
 **/
class ModifierExample extends OrderModifier {

// ######################################## *** model defining static variables (e.g. $db, $has_one)

	/**
	 * add extra fields as you need them.
	 *
	 **/
	public static $db = array(
		"MyField" => "Varchar",
		"MyReduction" => "Currency"
	);

// ######################################## *** cms variables + functions (e.g. getCMSFields, $searchableFields)

	/**
	 * standard SS method
	 */
	function getCMSFields() {
		$fields = parent::getCMSFields();
		return $fields;
	}

	public static $singular_name = "Modifier Example";
		function i18n_singular_name() { return _t("ModifierExample.MODIFIEREXAMPLE", "Modifier Example");}

	public static $plural_name = "Modifier Examples";
		function i18n_plural_name() { return _t("ModifierExample.MODIFIEREXAMPLES", "Modifier Examples");}

// ######################################## *** other (non) static variables (e.g. protected static $special_name_for_something, protected $order)


// ######################################## *** CRUD functions (e.g. canEdit)
// ######################################## *** init and update functions

	/**
	 * For all modifers with their own database fields, we need to include this...
	 * It will update each of the fields.
	 * Within this method, we need to create the methods
	 * Live{functionName}
	 * e.g LiveMyField() and LiveMyReduction() in this case...
	 * The OrderModifier already updates the basic database fields.
	 * @param Bool $force - run it, even if it has run already
	 */
	public function runUpdate($force = false) {
		if (isset($_GET['debug_profile'])) Profiler::mark('ModifierExample::runUpdate');
		if(!$this->IsRemoved()) {
			$this->checkField("MyField");
			$this->checkField("MyReduction");
		}
		if (isset($_GET['debug_profile'])) Profiler::unmark('ModifierExample::runUpdate');
		parent::runUpdate($force);
	}


	/**
	 * allows you to save a new value to MyField
	 * @param String $s
	 * @param Boolean $write - write to database (you may want to set this to false if you do several updates)
	 */
	public function updateMyField($s, $write = true) {
		$this->MyField = $s;
		if($write) {
			$this->write();
		}
	}

	/**
	 * allows you to save a new value to MyReduction
	 * @param Integer $int
	 */
	public function updateMyReduction($int, $write = true) {
		$this->MyReduction = $int;
		if($write) {
			$this->write();
		}
	}

// ######################################## *** form functions (e. g. Showform and getform)

	/**
	 * standard OrderModifier Method
	 * Should we show a form in the checkout page for this modifier?
	 */
	public function ShowForm() {
		return $this->Order()->Items();
	}

	/**
	 * Should the form be included in the editable form
	 * on the checkout page?
	 * @return Boolean
	 */
	public function ShowFormInEditableOrderTable() {
		if($this->exists()) {
			return $this->ID % 2 ? true : false;
		}
		return false;
	}

	/**
	 * Should the form be included in the editable form
	 * on the checkout page?
	 * @return Boolean
	 */
	public function ShowFormOutsideEditableOrderTable() {
		return $this->ShowFormInEditableOrderTable() ? false : true;
	}
	/**
	 * standard OrderModifier Method
	 * This method returns the form for the checkout page.
	 * @param Object $controller = Controller object for form
	 * @return Object - ModifierExample_Form
	 */
	function getModifierForm($optionalController = null, $optionalValidator = null) {
		$fields = new FieldSet();
		$fields->push($this->headingField());
		$fields->push($this->descriptionField());
		$fields->push(new TextField('MyField', "enter value for testing", $this->MyField));
		$fields->push(new NumericField('MyReduction', "what discount would you like?", $this->MyReduction));
		$actions = new FieldSet(
			new FormAction('submit', 'Update Order')
		);
		return new ModifierExample_Form($optionalController, 'ModifierExample', $fields, $actions, $optionalValidator);
	}

// ######################################## *** template functions (e.g. ShowInTable, TableTitle, etc...) ... USES DB VALUES

	/**
	 * standard OrderModifer Method
	 * Tells us if the modifier should take up a row in the table on the checkout page.
	 * @return Boolean
	 */
	public function ShowInTable() {
		return true;
	}

	/**
	 * standard OrderModifer Method
	 * Tells us if the modifier can be removed (hidden / turned off) from the order.
	 * @return Boolean
	 */
	public function CanBeRemoved() {
		return true;
	}

// ######################################## ***  inner calculations.... USES CALCULATED VALUES



// ######################################## *** calculate database fields: protected function Live[field name]  ... USES CALCULATED VALUES

	/**
	 * if we want to change the default value for the Name field
	 * (defined in the OrderModifer class) then we can do this
	 * as shown in the method below.
	 * You may choose to return an empty string or just a standard message.
	 **/
	protected function LiveName() {
		return "EXAMPLE: ".$this->LiveMyField();
	}

	protected function LiveMyField() {
		return $this->MyField;
	}

	protected function LiveMyReduction() {
		return $this->MyReduction;
	}

	protected function LiveCalculatedTotal() {
		return (intval($this->MyReduction) - 0) * -1;
	}

	public function LiveTableValue() {
		return $this->LiveCalculatedTotal();
	}


// ######################################## *** Type Functions (IsChargeable, IsDeductable, IsNoChange, IsRemoved)



// ######################################## *** standard database related functions (e.g. onBeforeWrite, onAfterWrite, etc...)

	function onBeforeWrite() {
		parent::onBeforeWrite();
	}

	function onBeforeRemove(){
		$this->MyReduction = 0;
		$this->MyField = "";
		parent::onBeforeRemove();
	}

// ######################################## *** AJAX related functions
	/**
	* some modifiers can be hidden after an ajax update (e.g. if someone enters a discount coupon and it does not exist).
	* There might be instances where ShowInTable (the starting point) is TRUE and HideInAjaxUpdate return false.
	*@return Boolean
	**/
	public function HideInAjaxUpdate() {
		//we check if the parent wants to hide it...
		//we need to do this first in case it is being removed.
		if(parent::HideInAjaxUpdate()) {
			return true;
		}
		// we do NOT hide it if values have been entered
		if($this->MyField && $this->MyReduction) {
			return false;
		}
		return true;
	}
// ######################################## *** debug functions

}

class ModifierExample_Form extends OrderModifierForm {
	/**
	 *
	 */
	function __construct($optionalController = null, $name,FieldSet $fields, FieldSet $actions,$optionalValidator = null) {
		parent::__construct($optionalController, $name,$fields,$actions,$optionalValidator);
		Requirements::javascript("ecommerce_modifier_example/javascript/ModifierExample.js");
	}

	public function submit($data, $form) {
		$order = ShoppingCart::current_order();
		if($order) {
			if($modifiers = $order->Modifiers("ModifierExample")) {
				foreach($modifiers as $modifier) {
					if(isset($data['MyField'])) {
						$modifier->updateMyField(Convert::raw2sql($data["MyField"]), false);
					}
					if(isset($data['MyReduction'])) {
						$modifier->updateMyReduction(floatval($data["MyReduction"]), false);
					}
					$modifier->write();
				}
				return ShoppingCart::singleton()->setMessageAndReturn(_t("ModifierExample.UPDATED", "Updated modifier successfully.", "good"));
			}
		}
		return ShoppingCart::singleton()->setMessageAndReturn(_t("ModifierExample.NOTUPDATED", "Modifier not successfully updated.", "bad"));
	}
}
