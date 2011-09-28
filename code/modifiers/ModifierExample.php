<?php

/**
 * @author Nicolaas [at] sunnysideup.co.nz
 * @package: ecommerce
 * @sub-package: ecommerce_delivery
 * @description: Shipping calculation scheme based on SimpleShippingModifier.
 * It lets you set fixed shipping costs, or a fixed
 * cost for each region you're delivering to.
 */
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

	function getCMSFields() {
		$fields = parent::getCMSFields();
		return $fields;
	}

	public static $singular_name = "Modifier Example";
		function i18n_single_name() { return _t("ModifierExample.MODIFIEREXAMPLE", "Modifier Example");}

	public static $plural_name = "Modifier Examples";
		function i18n_plural_name() { return _t("ModifierExample.MODIFIEREXAMPLES", "Modifier Examples");}

// ######################################## *** other (non) static variables (e.g. protected static $special_name_for_something, protected $order)

	protected static $form_header = 'Modifier Example';
		static function set_form_header(string $s) {self::$form_header = $s;}

// ######################################## *** CRUD functions (e.g. canEdit)
// ######################################## *** init and update functions

	/**
	 * For all modifers with their own database fields, we need to include this...
	 * It will update each of the fields.
	 * With this, we also need to create the methods
	 * Live{functionName}
	 * e.g LiveMyField() and LiveMyReduction() in this case...
	 **/

	public function runUpdate() {
		$this->checkField("MyField");
		$this->checkField("MyReduction");
		parent::runUpdate();
	}

	function updateMyField($s) {
		$this->MyField = $s;
	}

	function updateMyReduction($int) {
		$this->MyReduction = $int;
	}

// ######################################## *** form functions (e. g. showform and getform)


	public function showForm() {
		return $this->Order()->Items();
	}

	function getModifierForm($controller) {
		$fields = new FieldSet();
		$fields->push(new TextField('MyField', "enter value for testing", $this->MyField));
		$fields->push(new NumericField('MyReduction', "what discount would you like?", $this->MyReduction));
		$validator = null;
		$actions = new FieldSet(
			new FormAction('submit', 'Update Order')
		);
		return new ModifierExample_Form($controller, 'ModifierExample', $fields, $actions, $validator);
	}

// ######################################## *** template functions (e.g. ShowInTable, TableTitle, etc...) ... USES DB VALUES

	public function ShowInTable() {
		return true;
	}
	public function CanBeRemoved() {
		return true;
	}
	public function TableValue() {
		return $this->MyReduction;
	}

	public function TableTitle() {return $this->getTableTitle();}
	public function getTableTitle() {
		return $this->MyField;
	}

// ######################################## ***  inner calculations.... USES CALCULATED VALUES



// ######################################## *** calculate database fields: protected function Live[field name]  ... USES CALCULATED VALUES

	/**
	 * if we want to change the default value for the Name field
	 * (defined in the OrderModifer class) then we can do this
	 * as shown in the method below.
	 * You may choose to return an empty string or just a standard message.
	 *
	 *
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

	protected function LiveCalculationValue() {
		return (intval($this->MyReduction) - 0) * -1;
	}


// ######################################## *** Type Functions (IsChargeable, IsDeductable, IsNoChange, IsRemoved)



// ######################################## *** standard database related functions (e.g. onBeforeWrite, onAfterWrite, etc...)

	function onBeforeWrite() {
		parent::onBeforeWrite();
	}

// ######################################## *** AJAX related functions
// ######################################## *** debug functions

}

class ModifierExample_Form extends OrderModifierForm {

	function __construct($optionalController = null, $name,FieldSet $fields, FieldSet $actions,$validator = null) {
		parent::__construct($optionalController, $name,$fields,$actions,$validator);
		Requirements::javascript("ecommerce_modifier_example/javascript/ModifierExample.js");
	}

	public function submit($data, $form) {
		$order = ShoppingCart::current_order();
		$modifiers = $order->Modifiers();
		foreach($modifiers as $modifier) {
			if (get_class($modifier) == 'ModifierExample') {
				if(isset($data['MyField'])) {
					$modifier->updateMyField(Convert::raw2sql($data["MyField"]));
					$modifier->updateMyReduction(floatval($data["MyReduction"]));
					$modifier->write();
					return ShoppingCart::singleton()->setMessageAndReturn(_t("ModifierExample.UPDATED", "Updated modifier successfully.", "good"));
				}
			}
		}
		return ShoppingCart::singleton()->setMessageAndReturn(_t("ModifierExample.UPDATED", "Updated not successfully updated.", "bad"));
	}
}
