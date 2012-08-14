<?php
	/**
	 * @package toolkit
	 */

	/**
	 * The Tag List field is really a different interface for the Select Box
	 * field, offering a tag interface that can have static suggestions,
	 * suggestions from another field or a dynamic list based on what an Author
	 * has previously used for this field.
	 */

	Class fieldTagList extends Field {
		public function __construct(){
			parent::__construct();
			$this->_name = __('Tag List');
		}

	/*-------------------------------------------------------------------------
		Definition:
	-------------------------------------------------------------------------*/

		public function canFilter() {
			return true;
		}

		public function canImport(){
			return true;
		}

		public function canPrePopulate(){
			return true;
		}

		public function requiresSQLGrouping() {
			return true;
		}

		public function allowDatasourceParamOutput(){
			return true;
		}

	/*-------------------------------------------------------------------------
		Setup:
	-------------------------------------------------------------------------*/

		public function createTable(){
			return Symphony::Database()->query("
				CREATE TABLE IF NOT EXISTS `tbl_entries_data_" . $this->get('id') . "` (
				  `id` int(11) unsigned NOT NULL auto_increment,
				  `entry_id` int(11) unsigned NOT NULL,
				  `handle` varchar(255) default NULL,
				  `value` varchar(255) default NULL,
				  PRIMARY KEY  (`id`),
				  KEY `entry_id` (`entry_id`),
				  KEY `handle` (`handle`),
				  KEY `value` (`value`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
			");
		}

	/*-------------------------------------------------------------------------
		Utilities:
	-------------------------------------------------------------------------*/

		public function set($field, $value){
			if($field == 'pre_populate_source' && !is_array($value)) $value = preg_split('/\s*,\s*/', $value, -1, PREG_SPLIT_NO_EMPTY);
			$this->_fields[$field] = $value;
		}

		public function findAllTags(){
			if(!is_array($this->get('pre_populate_source'))) return;

			$values = array();

			foreach($this->get('pre_populate_source') as $item){
				$result = Symphony::Database()->fetchCol('value', sprintf(
					"SELECT DISTINCT `value` FROM tbl_entries_data_%d ORDER BY `value` ASC",
					($item == 'existing' ? $this->get('id') : $item)
				));

				if(!is_array($result) || empty($result)) continue;

				$values = array_merge($values, $result);
			}

			return array_unique($values);
		}

		private static function __tagArrayToString(array $tags){
			if(empty($tags)) return NULL;

			sort($tags);

			return implode(', ', $tags);
		}

	/*-------------------------------------------------------------------------
		Settings:
	-------------------------------------------------------------------------*/

		public function findDefaults(array &$settings){
			if(!isset($settings['pre_populate_source'])) $settings['pre_populate_source'] = array('existing');
		}

		public function displaySettingsPanel(XMLElement &$wrapper, $errors = null) {
			parent::displaySettingsPanel($wrapper, $errors);

			$label = Widget::Label(__('Suggestion List'));

			$sections = SectionManager::fetch(NULL, 'ASC', 'name');
			$field_groups = array();

			if(is_array($sections) && !empty($sections))
				foreach($sections as $section) $field_groups[$section->get('id')] = array('fields' => $section->fetchFields(), 'section' => $section);

			$options = array(
				array('existing', (in_array('existing', $this->get('pre_populate_source'))), __('Existing Values')),
			);

			foreach($field_groups as $group){

				if(!is_array($group['fields'])) continue;

				$fields = array();
				foreach($group['fields'] as $f){
					if($f->get('id') != $this->get('id') && $f->canPrePopulate()) $fields[] = array($f->get('id'), (in_array($f->get('id'), $this->get('pre_populate_source'))), $f->get('label'));
				}

				if(is_array($fields) && !empty($fields)) $options[] = array('label' => $group['section']->get('name'), 'options' => $fields);
			}

			$label->appendChild(Widget::Select('fields['.$this->get('sortorder').'][pre_populate_source][]', $options, array('multiple' => 'multiple')));
			$wrapper->appendChild($label);

			$this->buildValidationSelect($wrapper, $this->get('validator'), 'fields['.$this->get('sortorder').'][validator]');

			$this->appendShowColumnCheckbox($wrapper);
		}

		public function commit(){
			if(!parent::commit()) return false;

			$id = $this->get('id');

			if($id === false) return false;

			$fields = array();

			$fields['pre_populate_source'] = (is_null($this->get('pre_populate_source')) ? NULL : implode(',', $this->get('pre_populate_source')));
			$fields['validator'] = ($fields['validator'] == 'custom' ? NULL : $this->get('validator'));

			return FieldManager::saveSettings($id, $fields);
		}

	/*-------------------------------------------------------------------------
		Publish:
	-------------------------------------------------------------------------*/

		public function displayPublishPanel(XMLElement &$wrapper, $data = null, $flagWithError = null, $fieldnamePrefix = null, $fieldnamePostfix = null, $entry_id = null){
			$value = NULL;
			if(isset($data['value'])){
				$value = (is_array($data['value']) ? self::__tagArrayToString($data['value']) : $data['value']);
			}

			$label = Widget::Label($this->get('label'));

			$label->appendChild(
				Widget::Input('fields'.$fieldnamePrefix.'['.$this->get('element_name').']'.$fieldnamePostfix, (strlen($value) != 0 ? General::sanitize($value) : NULL))
			);

			if($flagWithError != NULL) $wrapper->appendChild(Widget::Error($label, $flagWithError));
			else $wrapper->appendChild($label);

			if($this->get('pre_populate_source') != NULL){

				$existing_tags = $this->findAllTags();

				if(is_array($existing_tags) && !empty($existing_tags)){
					$taglist = new XMLElement('ul');
					$taglist->setAttribute('class', 'tags');

					foreach($existing_tags as $tag) {
						$taglist->appendChild(
							new XMLElement('li', General::sanitize($tag))
						);
					}

					$wrapper->appendChild($taglist);
				}
			}
		}

		public function checkPostFieldData($data, &$message, $entry_id = null){
			$message = NULL;

			if($this->get('validator')) {
				$data = preg_split('/\,\s*/i', $data, -1, PREG_SPLIT_NO_EMPTY);
				$data = array_map('trim', $data);

				if(empty($data)) return self::__OK__;

				if(!General::validateString($data, $this->get('validator'))) {
					$message = __("'%s' contains invalid data. Please check the contents.", array($this->get('label')));
					return self::__INVALID_FIELDS__;
				}
			}

			return self::__OK__;
		}

		public function processRawFieldData($data, &$status, &$message=null, $simulate=false, $entry_id=NULL){
			$status = self::__OK__;

			$data = preg_split('/\,\s*/i', $data, -1, PREG_SPLIT_NO_EMPTY);
			$data = array_map('trim', $data);

			if(empty($data)) return;

			// Do a case insensitive removal of duplicates
			$data = General::array_remove_duplicates($data, true);

			sort($data);

			$result = array();
			foreach($data as $value){
				$result['value'][] = $value;
				$result['handle'][] = Lang::createHandle($value);
			}

			return $result;
		}

	/*-------------------------------------------------------------------------
		Output:
	-------------------------------------------------------------------------*/

		public function appendFormattedElement(XMLElement &$wrapper, $data, $encode = false, $mode = null, $entry_id = null) {
			if (!is_array($data) or empty($data)) return;

			$list = new XMLElement($this->get('element_name'));

			if (!is_array($data['handle']) and !is_array($data['value'])) {
				$data = array(
					'handle'	=> array($data['handle']),
					'value'		=> array($data['value'])
				);
			}

			foreach ($data['value'] as $index => $value) {
				$list->appendChild(new XMLElement(
					'item', General::sanitize($value), array(
						'handle'	=> $data['handle'][$index]
					)
				));
			}

			$wrapper->appendChild($list);
		}

		public function prepareTableValue($data, XMLElement $link=NULL, $entry_id = null){
			if(!is_array($data) || empty($data)) return;

			$value = NULL;
			if(isset($data['value'])){
				$value = (is_array($data['value']) ? self::__tagArrayToString($data['value']) : $data['value']);
			}

			return parent::prepareTableValue(array('value' => General::sanitize($value)), $link, $entry_id = null);
		}

		public function getParameterPoolValue($data, $entry_id = null) {
			return $data['handle'];
		}

	/*-------------------------------------------------------------------------
		Filtering:
	-------------------------------------------------------------------------*/

		public function displayDatasourceFilterPanel(XMLElement &$wrapper, $data = null, $errors = null, $fieldnamePrefix=NULL, $fieldnamePostfix=NULL){
			parent::displayDatasourceFilterPanel($wrapper, $data, $errors, $fieldnamePrefix, $fieldnamePostfix);

			if($this->get('pre_populate_source') != NULL){

				$existing_tags = $this->findAllTags();

				if(is_array($existing_tags) && !empty($existing_tags)){
					$taglist = new XMLElement('ul');
					$taglist->setAttribute('class', 'tags');

					foreach($existing_tags as $tag) {
						$taglist->appendChild(
							new XMLElement('li', General::sanitize($tag))
						);
					}

					$wrapper->appendChild($taglist);
				}
			}
		}

		public function buildDSRetrievalSQL($data, &$joins, &$where, $andOperation = false) {
			$field_id = $this->get('id');

			if (self::isFilterRegex($data[0])) {
				$this->buildRegexSQL($data[0], array('value', 'handle'), $joins, $where);
			}
			else if ($andOperation) {
				foreach ($data as $value) {
					$this->_key++;
					$value = $this->cleanValue($value);
					$joins .= "
						LEFT JOIN
							`tbl_entries_data_{$field_id}` AS t{$field_id}_{$this->_key}
							ON (e.id = t{$field_id}_{$this->_key}.entry_id)
					";
					$where .= "
						AND (
							t{$field_id}_{$this->_key}.value = '{$value}'
							OR t{$field_id}_{$this->_key}.handle = '{$value}'
						)
					";
				}
			}
			else {
				if (!is_array($data)) $data = array($data);

				foreach ($data as &$value) {
					$value = $this->cleanValue($value);
				}

				$this->_key++;
				$data = implode("', '", $data);
				$joins .= "
					LEFT JOIN
						`tbl_entries_data_{$field_id}` AS t{$field_id}_{$this->_key}
						ON (e.id = t{$field_id}_{$this->_key}.entry_id)
				";
				$where .= "
					AND (
						t{$field_id}_{$this->_key}.value IN ('{$data}')
						OR t{$field_id}_{$this->_key}.handle IN ('{$data}')
					)
				";
			}

			return true;
		}

	}
