<?php
	/**
	 * @package content
	 */

	/**
	 * The Publish page is where the majority of an Authors time will
	 * be spent in Symphony with adding, editing and removing entries
	 * from Sections. This Page controls the entries table as well as
	 * the Entry creation screens.
	 */
	require_once(TOOLKIT . '/class.administrationpage.php');
	require_once(TOOLKIT . '/class.entrymanager.php');
	require_once(TOOLKIT . '/class.sectionmanager.php');
	require_once(CONTENT . '/class.sortable.php');

	Class contentPublish extends AdministrationPage{

		public $_errors = array();

		public function sort(&$sort, &$order, $params) {
			$section = $params['current-section'];

			// If `?unsort` is appended to the URL, then sorting information are reverted
			// to their defaults
			if($params['unsort']) {
				$section->setSortingField($section->getDefaultSortingField(), false);
				$section->setSortingOrder('asc');

				redirect(Administration::instance()->getCurrentPageURL());
			}

			// By default, sorting information are retrieved from
			// the filesystem and stored inside the `Configuration` object
			if(is_null($sort) && is_null($order)) {
				$sort = $section->getSortingField();
				$order = $section->getSortingOrder();

				// Set the sorting in the `EntryManager` for subsequent use
				EntryManager::setFetchSorting($sort, $order);
			}
			else {
				// Ensure that this field is infact sortable, otherwise
				// fallback to IDs
				if(($field = FieldManager::fetch($sort)) instanceof Field && !$field->isSortable()) {
					$sort = $section->getDefaultSortingField();
				}

				// If the sort order or direction differs from what is saved,
				// update the config file and reload the page
				if($sort != $section->getSortingField() || $order != $section->getSortingOrder()){
					$section->setSortingField($sort, false);
					$section->setSortingOrder($order);

					redirect(Administration::instance()->getCurrentPageURL() . $params['filters']);
				}
			}

		}

		public function action(){
			$this->__switchboard('action');
		}

		public function __switchboard($type='view'){

			$function = ($type == 'action' ? '__action' : '__view') . ucfirst($this->_context['page']);

			if(!method_exists($this, $function)) {
				// If there is no action function, just return without doing anything
				if($type == 'action') return;

				Administration::instance()->errorPageNotFound();
			}

			$this->$function();
		}

		public function view(){
			$this->__switchboard();
		}

		public function __viewIndex(){
			if(!$section_id = SectionManager::fetchIDFromHandle($this->_context['section_handle'])) {
				Administration::instance()->customError(__('Unknown Section'), __('The Section, %s, could not be found.', array('<code>' . $this->_context['section_handle'] . '</code>')));
			}

			$section = SectionManager::fetch($section_id);

			$this->setPageType('table');
			$this->setTitle(__('%1$s &ndash; %2$s', array($section->get('name'), __('Symphony'))));

			$filters = array();
			$filter_querystring = $prepopulate_querystring = $where = $joins = NULL;
			$current_page = (isset($_REQUEST['pg']) && is_numeric($_REQUEST['pg']) ? max(1, intval($_REQUEST['pg'])) : 1);

			if(isset($_REQUEST['filter'])) {
				// legacy implementation, convert single filter to an array
				// split string in the form ?filter=handle:value
				if(!is_array($_REQUEST['filter'])) {
					list($field_handle, $filter_value) = explode(':', $_REQUEST['filter'], 2);
					$filters[$field_handle] = rawurldecode($filter_value);
				} else {
					$filters = $_REQUEST['filter'];
				}

				foreach($filters as $handle => $value) {
					$field_id = FieldManager::fetchFieldIDFromElementName(
						Symphony::Database()->cleanValue($handle),
						$section->get('id')
					);

					$field = FieldManager::fetch($field_id);

					if($field instanceof Field) {
						// For deprecated reasons, call the old, typo'd function name until the switch to the
						// properly named buildDSRetrievalSQL function.
						$field->buildDSRetrivalSQL(array($value), $joins, $where, false);
						$filter_querystring .= sprintf("filter[%s]=%s&amp;", $handle, rawurlencode($value));
						$prepopulate_querystring .= sprintf("prepopulate[%d]=%s&amp;", $field_id, rawurlencode($value));
					}
					else {
						unset($filters[$handle]);
					}
				}

				$filter_querystring = preg_replace("/&amp;$/", '', $filter_querystring);
				$prepopulate_querystring = preg_replace("/&amp;$/", '', $prepopulate_querystring);
			}

			Sortable::initialize($this, $entries, $sort, $order, array(
				'current-section' => $section,
				'filters' => ($filter_querystring ? "&amp;" . $filter_querystring : ''),
				'unsort' => isset($_REQUEST['unsort'])
			));

			$this->Form->setAttribute('action', Administration::instance()->getCurrentPageURL(). '?pg=' . $current_page.($filter_querystring ? "&amp;" . $filter_querystring : ''));

			$subheading_buttons = array(
				Widget::Anchor(__('Create New'), Administration::instance()->getCurrentPageURL().'new/'.($prepopulate_querystring ? '?' . $prepopulate_querystring : ''), __('Create a new entry'), 'create button', NULL, array('accesskey' => 'c'))
			);

			// Only show the Edit Section button if the Author is a developer. #938 ^BA
			if(Administration::instance()->Author->isDeveloper()) {
				array_unshift($subheading_buttons, Widget::Anchor(__('Edit Section'), SYMPHONY_URL . '/blueprints/sections/edit/' . $section_id, __('Edit Section Configuration'), 'button'));
			}

			$this->appendSubheading($section->get('name'), $subheading_buttons);

			// Check that the filtered query fails that the filter is dropped and an
			// error is logged. #841 ^BA
			try {
				$entries = EntryManager::fetchByPage($current_page, $section_id, Symphony::Configuration()->get('pagination_maximum_rows', 'symphony'), $where, $joins);
			}
			catch(DatabaseException $ex) {
				$this->pageAlert(__('An error occurred while retrieving filtered entries. Showing all entries instead.'), Alert::ERROR);
				$filter_querystring = null;
				Symphony::Log()->pushToLog(sprintf(
						'%s - %s%s%s',
						$section->get('name') . ' Publish Index',
						$ex->getMessage(),
						($ex->getFile() ? " in file " .  $ex->getFile() : null),
						($ex->getLine() ? " on line " . $ex->getLine() : null)
					),
					E_NOTICE, true
				);
				$entries = EntryManager::fetchByPage($current_page, $section_id, Symphony::Configuration()->get('pagination_maximum_rows', 'symphony'));
			}

			$visible_columns = $section->fetchVisibleColumns();
			$columns = array();

			if(is_array($visible_columns) && !empty($visible_columns)){

				foreach($visible_columns as $column){
					$columns[] = array(
						'label' => $column->get('label'),
						'sortable' => $column->isSortable(),
						'handle' => $column->get('id'),
						'attrs' => array(
							'id' => 'field-' . $column->get('id'),
							'class' => 'field-' . $column->get('type')
						)
					);
				}
			}
			else {
				$columns[] = array(
					'label' => __('ID'),
					'sortable' => true,
					'handle' => 'id'
				);
			}

			$aTableHead = Sortable::buildTableHeaders(
				$columns, $sort, $order,
				($filter_querystring) ? "&amp;" . $filter_querystring : ''
			);

			$child_sections = array();
			$associated_sections = $section->fetchAssociatedSections(true);
			if(is_array($associated_sections) && !empty($associated_sections)){
				foreach($associated_sections as $key => $as){
					$child_sections[$key] = SectionManager::fetch($as['child_section_id']);
					$aTableHead[] = array($child_sections[$key]->get('name'), 'col');
				}
			}

			/**
			 * Allows the creation of custom table columns for each entry. Called
			 * after all the Section Visible columns have been added as well
			 * as the Section Associations
			 *
			 * @delegate AddCustomPublishColumn
			 * @since Symphony 2.2
			 * @param string $context
			 * '/publish/'
			 * @param array $tableHead
			 * An array of the current columns, passed by reference
			 * @param integer $section_id
			 * The current Section ID
			 */
			Symphony::ExtensionManager()->notifyMembers('AddCustomPublishColumn', '/publish/', array('tableHead' => &$aTableHead, 'section_id' => $section->get('id')));

			// Table Body
			$aTableBody = array();

			if(!is_array($entries['records']) || empty($entries['records'])){

				$aTableBody = array(
					Widget::TableRow(array(Widget::TableData(__('None found.'), 'inactive', NULL, count($aTableHead))), 'odd')
				);
			}
			else {

				$field_pool = array();
				if(is_array($visible_columns) && !empty($visible_columns)){
					foreach($visible_columns as $column){
						$field_pool[$column->get('id')] = $column;
					}
				}
				$link_column = end(array_reverse($visible_columns));
				reset($visible_columns);

				foreach($entries['records'] as $entry) {
					$tableData = array();

					// Setup each cell
					if(!is_array($visible_columns) || empty($visible_columns)) {
						$tableData[] = Widget::TableData(Widget::Anchor($entry->get('id'), Administration::instance()->getCurrentPageURL() . 'edit/' . $entry->get('id') . '/'));
					}
					else {
						$link = Widget::Anchor(
							__('None'),
							Administration::instance()->getCurrentPageURL() . 'edit/' . $entry->get('id') . '/'.($filter_querystring ? '?' . $prepopulate_querystring : ''),
							$entry->get('id'),
							'content'
						);

						foreach ($visible_columns as $position => $column) {
							$data = $entry->getData($column->get('id'));
							$field = $field_pool[$column->get('id')];

							$value = $field->prepareTableValue($data, ($column == $link_column) ? $link : null, $entry->get('id'));

							if (!is_object($value) && (strlen(trim($value)) == 0 || $value == __('None'))) {
								$value = ($position == 0 ? $link->generate() : __('None'));
							}

							if ($value == __('None')) {
								$tableData[] = Widget::TableData($value, 'inactive field-' . $column->get('type') . ' field-' . $column->get('id'));
							}
							else {
								$tableData[] = Widget::TableData($value, 'field-' . $column->get('type') . ' field-' . $column->get('id'));
							}

							unset($field);
						}
					}

					if(is_array($child_sections) && !empty($child_sections)){
						foreach($child_sections as $key => $as){

							$field = FieldManager::fetch((int)$associated_sections[$key]['child_section_field_id']);

							$parent_section_field_id = (int)$associated_sections[$key]['parent_section_field_id'];

							if(!is_null($parent_section_field_id)){
								$search_value = $field->fetchAssociatedEntrySearchValue(
									$entry->getData($parent_section_field_id),
									$parent_section_field_id,
									$entry->get('id')
								);
							}
							else {
								$search_value = $entry->get('id');
							}

							if(!is_array($search_value)) {
								$associated_entry_count = $field->fetchAssociatedEntryCount($search_value);

								$tableData[] = Widget::TableData(
									Widget::Anchor(
										sprintf('%d &rarr;', max(0, intval($associated_entry_count))),
										sprintf(
											'%s/publish/%s/?filter=%s:%s',
											SYMPHONY_URL,
											$as->get('handle'),
											$field->get('element_name'),
											rawurlencode($search_value)
										),
										$entry->get('id'),
										'content')
								);
							}
						}
					}

					/**
					 * Allows Extensions to inject custom table data for each Entry
					 * into the Publish Index
					 *
					 * @delegate AddCustomPublishColumnData
					 * @since Symphony 2.2
					 * @param string $context
					 * '/publish/'
					 * @param array $tableData
					 *  An array of `Widget::TableData`, passed by reference
					 * @param integer $section_id
					 *  The current Section ID
					 * @param Entry $entry_id
					 *  The entry object, please note that this is by error and this will
					 *  be removed in Symphony 2.4. The entry object is available in
					 *  the 'entry' key as of Symphony 2.3.1.
					 * @param Entry $entry
					 *  The entry object for this row
					 */
					Symphony::ExtensionManager()->notifyMembers('AddCustomPublishColumnData', '/publish/', array(
						'tableData' => &$tableData,
						'section_id' => $section->get('id'),
						'entry_id' => $entry,
						'entry' => $entry
					));

					$tableData[count($tableData) - 1]->appendChild(Widget::Input('items['.$entry->get('id').']', NULL, 'checkbox'));

					// Add a row to the body array, assigning each cell to the row
					$aTableBody[] = Widget::TableRow($tableData, NULL, 'id-' . $entry->get('id'));
				}
			}

			$table = Widget::Table(
				Widget::TableHead($aTableHead),
				NULL,
				Widget::TableBody($aTableBody),
				'selectable'
			);

			$this->Form->appendChild($table);

			$tableActions = new XMLElement('div');
			$tableActions->setAttribute('class', 'actions');

			$options = array(
				array(NULL, false, __('With Selected...')),
				array('delete', false, __('Delete'), 'confirm', null, array(
					'data-message' => __('Are you sure you want to delete the selected entries?')
				))
			);

			$toggable_fields = $section->fetchToggleableFields();

			if (is_array($toggable_fields) && !empty($toggable_fields)) {
				$index = 2;

				foreach ($toggable_fields as $field) {
					$options[$index] = array('label' => __('Set %s', array($field->get('label'))), 'options' => array());

					foreach ($field->getToggleStates() as $value => $state) {
						$options[$index]['options'][] = array('toggle-' . $field->get('id') . '-' . $value, false, $state);
					}

					$index++;
				}
			}

			$tableActions->appendChild(Widget::Apply($options));
			$this->Form->appendChild($tableActions);

			if($entries['total-pages'] > 1){
				$ul = new XMLElement('ul');
				$ul->setAttribute('class', 'page');

				// First
				$li = new XMLElement('li');
				if($current_page > 1) $li->appendChild(Widget::Anchor(__('First'), Administration::instance()->getCurrentPageURL(). '?pg=1'.($filter_querystring ? "&amp;" . $filter_querystring : '')));
				else $li->setValue(__('First'));
				$ul->appendChild($li);

				// Previous
				$li = new XMLElement('li');
				if($current_page > 1) $li->appendChild(Widget::Anchor(__('&larr; Previous'), Administration::instance()->getCurrentPageURL(). '?pg=' . ($current_page - 1).($filter_querystring ? "&amp;" . $filter_querystring : '')));
				else $li->setValue(__('&larr; Previous'));
				$ul->appendChild($li);

				// Summary
				$li = new XMLElement('li');

				$li->setAttribute('title', __('Viewing %1$s - %2$s of %3$s entries', array(
					$entries['start'],
					($current_page != $entries['total-pages']) ? $current_page * Symphony::Configuration()->get('pagination_maximum_rows', 'symphony') : $entries['total-entries'],
					$entries['total-entries']
				)));

				$pgform = Widget::Form(Administration::instance()->getCurrentPageURL(),'get','paginationform');
				$pgmax = max($current_page, $entries['total-pages']);
				$pgform->appendChild(Widget::Input('pg', NULL, 'text', array(
					'data-active' => __('Go to page …'),
					'data-inactive' => __('Page %1$s of %2$s', array((string)$current_page, $pgmax)),
					'data-max' => $pgmax
				)));

				$li->appendChild($pgform);
				$ul->appendChild($li);

				// Next
				$li = new XMLElement('li');
				if($current_page < $entries['total-pages']) $li->appendChild(Widget::Anchor(__('Next &rarr;'), Administration::instance()->getCurrentPageURL(). '?pg=' . ($current_page + 1).($filter_querystring ? "&amp;" . $filter_querystring : '')));
				else $li->setValue(__('Next &rarr;'));
				$ul->appendChild($li);

				// Last
				$li = new XMLElement('li');
				if($current_page < $entries['total-pages']) $li->appendChild(Widget::Anchor(__('Last'), Administration::instance()->getCurrentPageURL(). '?pg=' . $entries['total-pages'].($filter_querystring ? "&amp;" . $filter_querystring : '')));
				else $li->setValue(__('Last'));
				$ul->appendChild($li);

				$this->Contents->appendChild($ul);
			}
		}

		public function __actionIndex(){
			$checked = (is_array($_POST['items'])) ? array_keys($_POST['items']) : null;

			if(is_array($checked) && !empty($checked)){
				switch($_POST['with-selected']) {

					case 'delete':

						/**
						 * Prior to deletion of entries. An array of Entry ID's is provided which
						 * can be manipulated. This delegate was renamed from `Delete` to `EntryPreDelete`
						 * in Symphony 2.3.
						 *
						 * @delegate EntryPreDelete
						 * @param string $context
						 * '/publish/'
						 * @param array $entry_id
						 *  An array of Entry ID's passed by reference
						 */
						Symphony::ExtensionManager()->notifyMembers('EntryPreDelete', '/publish/', array('entry_id' => &$checked));

						EntryManager::delete($checked);

						/**
						 * After the deletion of entries, this delegate provides an array of Entry ID's
						 * that were deleted.
						 *
						 * @since Symphony 2.3
						 * @delegate EntryPostDelete
						 * @param string $context
						 * '/publish/'
						 * @param array $entry_id
						 *  An array of Entry ID's that were deleted.
						 */
						Symphony::ExtensionManager()->notifyMembers('EntryPostDelete', '/publish/', array('entry_id' => $checked));

						redirect($_SERVER['REQUEST_URI']);

					default:

						list($option, $field_id, $value) = explode('-', $_POST['with-selected'], 3);

						if($option == 'toggle'){

							$field = FieldManager::fetch($field_id);
							$fields = array($field->get('element_name') => $value);

							$section = SectionManager::fetch($field->get('parent_section'));

							foreach($checked as $entry_id){
								$entry = EntryManager::fetch($entry_id);
								$existing_data = $entry[0]->getData($field_id);
								$entry[0]->setData($field_id, $field->toggleFieldData(is_array($existing_data) ? $existing_data : array(), $value, $entry_id));

								/**
								 * Just prior to editing of an Entry
								 *
								 * @delegate EntryPreEdit
								 * @param string $context
								 * '/publish/edit/'
								 * @param Section $section
								 * @param Entry $entry
								 * @param array $fields
								 */
								Symphony::ExtensionManager()->notifyMembers('EntryPreEdit', '/publish/edit/', array(
									'section' => $section,
									'entry' => &$entry[0],
									'fields' => $fields
								));

								$entry[0]->commit();

								/**
								 * Editing an entry. Entry object is provided.
								 *
								 * @delegate EntryPostEdit
								 * @param string $context
								 * '/publish/edit/'
								 * @param Section $section
								 * @param Entry $entry
								 * @param array $fields
								 */
								Symphony::ExtensionManager()->notifyMembers('EntryPostEdit', '/publish/edit/', array(
									'section' => $section,
									'entry' => $entry[0],
									'fields' => $fields
								));
							}

							redirect($_SERVER['REQUEST_URI']);

						}

						break;
				}
			}
		}

		public function __viewNew() {
			if(!$section_id = SectionManager::fetchIDFromHandle($this->_context['section_handle'])) {
				Administration::instance()->customError(__('Unknown Section'), __('The Section, %s, could not be found.', array('<code>' . $this->_context['section_handle'] . '</code>')));
			}

			$section = SectionManager::fetch($section_id);

			$this->setPageType('form');
			$this->Form->setAttribute('enctype', 'multipart/form-data');
			$this->Form->setAttribute('class', 'two columns');
			$this->setTitle(__('%1$s &ndash; %2$s', array($section->get('name'), __('Symphony'))));

			// Only show the Edit Section button if the Author is a developer. #938 ^BA
			if(Administration::instance()->Author->isDeveloper()) {
				$this->appendSubheading(__('Untitled'),
					Widget::Anchor(__('Edit Section'), SYMPHONY_URL . '/blueprints/sections/edit/' . $section_id, __('Edit Section Configuration'), 'button')
				);
			}
			else {
				$this->appendSubheading(__('Untitled'));
			}

			// Build filtered breadcrumb [#1378}
			if(isset($_REQUEST['prepopulate'])){
				$link = '?';
				foreach($_REQUEST['prepopulate'] as $field_id => $value) {
					$handle = FieldManager::fetchHandleFromID($field_id);
					$link .= "filter[$handle]=$value&amp;";
				}
				$link = preg_replace("/&amp;$/", '', $link);
			}
			else {
				$link = '';
			}

			$this->insertBreadcrumbs(array(
				Widget::Anchor($section->get('name'), SYMPHONY_URL . '/publish/' . $this->_context['section_handle'] . '/' . $link),
			));

			$this->Form->appendChild(Widget::Input('MAX_FILE_SIZE', Symphony::Configuration()->get('max_upload_size', 'admin'), 'hidden'));

			// If there is post data floating around, due to errors, create an entry object
			if (isset($_POST['fields'])) {
				$entry = EntryManager::create();
				$entry->set('section_id', $section_id);
				$entry->setDataFromPost($_POST['fields'], $error, true);
			}

			// Brand new entry, so need to create some various objects
			else {
				$entry = EntryManager::create();
				$entry->set('section_id', $section_id);
			}

			// Check if there is a field to prepopulate
			if (isset($_REQUEST['prepopulate'])) {
				foreach($_REQUEST['prepopulate'] as $field_id => $value) {
					$this->Form->prependChild(Widget::Input(
						"prepopulate[{$field_id}]",
						rawurlencode($value),
						'hidden'
					));

					// The actual pre-populating should only happen if there is not existing fields post data
					if(!isset($_POST['fields']) && $field = FieldManager::fetch($field_id)) {
						$entry->setData(
							$field->get('id'),
							$field->processRawFieldData($value, $error, $message, true)
						);
					}
				}
			}

			$primary = new XMLElement('fieldset');
			$primary->setAttribute('class', 'primary column');

			$sidebar_fields = $section->fetchFields(NULL, 'sidebar');
			$main_fields = $section->fetchFields(NULL, 'main');

			if ((!is_array($main_fields) || empty($main_fields)) && (!is_array($sidebar_fields) || empty($sidebar_fields))) {
				$message = __('Fields must be added to this section before an entry can be created.');

				if(Administration::instance()->Author->isDeveloper()) {
					$message .= ' <a href="' . SYMPHONY_URL . '/blueprints/sections/edit/' . $section->get('id') . '/" accesskey="c">'
					. __('Add fields')
					. '</a>';
				}

				$this->pageAlert($message, Alert::ERROR);
			}

			else {
				if (is_array($main_fields) && !empty($main_fields)) {
					foreach ($main_fields as $field) {
						$primary->appendChild($this->__wrapFieldWithDiv($field, $entry));
					}

					$this->Form->appendChild($primary);
				}

				if (is_array($sidebar_fields) && !empty($sidebar_fields)) {
					$sidebar = new XMLElement('fieldset');
					$sidebar->setAttribute('class', 'secondary column');

					foreach ($sidebar_fields as $field) {
						$sidebar->appendChild($this->__wrapFieldWithDiv($field, $entry));
					}

					$this->Form->appendChild($sidebar);
				}

				$div = new XMLElement('div');
				$div->setAttribute('class', 'actions');
				$div->appendChild(Widget::Input('action[save]', __('Create Entry'), 'submit', array('accesskey' => 's')));

				$this->Form->appendChild($div);
			}
		}

		public function __actionNew(){
			if(array_key_exists('save', $_POST['action']) || array_key_exists("done", $_POST['action'])) {
				$section_id = SectionManager::fetchIDFromHandle($this->_context['section_handle']);

				if(!$section = SectionManager::fetch($section_id)) {
					Administration::instance()->customError(__('Unknown Section'), __('The Section, %s, could not be found.', array('<code>' . $this->_context['section_handle'] . '</code>')));
				}

				$entry =& EntryManager::create();
				$entry->set('author_id', Administration::instance()->Author->get('id'));
				$entry->set('section_id', $section_id);
				$entry->set('creation_date', DateTimeObj::get('c'));
				$entry->set('modification_date', DateTimeObj::get('c'));

				$fields = $_POST['fields'];

				// Combine FILES and POST arrays, indexed by their custom field handles
				if(isset($_FILES['fields'])){
					$filedata = General::processFilePostData($_FILES['fields']);

					foreach($filedata as $handle => $data){
						if(!isset($fields[$handle])) $fields[$handle] = $data;
						elseif(isset($data['error']) && $data['error'] == 4) $fields['handle'] = NULL;
						else{

							foreach($data as $ii => $d){
								if(isset($d['error']) && $d['error'] == 4) $fields[$handle][$ii] = NULL;
								elseif(is_array($d) && !empty($d)){

									foreach($d as $key => $val)
										$fields[$handle][$ii][$key] = $val;
								}
							}
						}
					}
				}

				// Initial checks to see if the Entry is ok
				if(__ENTRY_FIELD_ERROR__ == $entry->checkPostData($fields, $this->_errors)) {
					$this->pageAlert(__('Some errors were encountered while attempting to save.'), Alert::ERROR);
				}

				// Secondary checks, this will actually process the data and attempt to save
				else if(__ENTRY_OK__ != $entry->setDataFromPost($fields, $errors)) {
					foreach($errors as $field_id => $message) {
						$this->pageAlert($message, Alert::ERROR);
					}
				}

				// Everything is awesome. Dance.
				else {
					/**
					 * Just prior to creation of an Entry
					 *
					 * @delegate EntryPreCreate
					 * @param string $context
					 * '/publish/new/'
					 * @param Section $section
					 * @param Entry $entry
					 * @param array $fields
					 */
					Symphony::ExtensionManager()->notifyMembers('EntryPreCreate', '/publish/new/', array('section' => $section, 'entry' => &$entry, 'fields' => &$fields));

					// Check to see if the dancing was premature
					if(!$entry->commit()){
						define_safe('__SYM_DB_INSERT_FAILED__', true);
						$this->pageAlert(NULL, Alert::ERROR);
					}

					else {
						/**
						 * Creation of an Entry. New Entry object is provided.
						 *
						 * @delegate EntryPostCreate
						 * @param string $context
						 * '/publish/new/'
						 * @param Section $section
						 * @param Entry $entry
						 * @param array $fields
						 */
						Symphony::ExtensionManager()->notifyMembers('EntryPostCreate', '/publish/new/', array('section' => $section, 'entry' => $entry, 'fields' => $fields));

						$prepopulate_querystring = '';
						if(isset($_POST['prepopulate'])){
							foreach($_POST['prepopulate'] as $field_id => $value) {
								$prepopulate_querystring .= sprintf("prepopulate[%s]=%s&", $field_id, rawurldecode($value));
							}
							$prepopulate_querystring = trim($prepopulate_querystring, '&');
						}

						redirect(sprintf(
							'%s/publish/%s/edit/%d/created/%s',
							SYMPHONY_URL,
							$this->_context['section_handle'],
							$entry->get('id'),
							(!empty($prepopulate_querystring) ? "?" . $prepopulate_querystring : NULL)
						));
					}
				}
			}
		}

		public function __viewEdit() {
			if(!$section_id = SectionManager::fetchIDFromHandle($this->_context['section_handle'])) {
				Administration::instance()->customError(__('Unknown Section'), __('The Section, %s, could not be found.', array('<code>' . $this->_context['section_handle'] . '</code>')));
			}

			$section = SectionManager::fetch($section_id);
			$entry_id = intval($this->_context['entry_id']);
			$base = '/publish/'.$this->_context['section_handle'] . '/';

			EntryManager::setFetchSorting('id', 'DESC');

			if(!$existingEntry = EntryManager::fetch($entry_id)) {
				Administration::instance()->customError(__('Unknown Entry'), __('The Entry, %s, could not be found.', array($entry_id)));
			}
			$existingEntry = $existingEntry[0];

			// If there is post data floating around, due to errors, create an entry object
			if (isset($_POST['fields'])) {
				$fields = $_POST['fields'];

				$entry =& EntryManager::create();
				$entry->set('id', $entry_id);
				$entry->set('author_id', $existingEntry->get('author_id'));
				$entry->set('section_id', $existingEntry->get('section_id'));
				$entry->set('creation_date', $existingEntry->get('creation_date'));
				$entry->set('modification_date', $existingEntry->get('modification_date'));
				$entry->setDataFromPost($fields, $errors, true);
			}

			// Editing an entry, so need to create some various objects
			else {
				$entry = $existingEntry;

				if (!$section) {
					$section = SectionManager::fetch($entry->get('section_id'));
				}
			}

			/**
			 * Just prior to rendering of an Entry edit form.
			 *
			 * @delegate EntryPreRender
			 * @param string $context
			 * '/publish/edit/'
			 * @param Section $section
			 * @param Entry $entry
			 * @param array $fields
			 */
			Symphony::ExtensionManager()->notifyMembers('EntryPreRender', '/publish/edit/', array(
				'section' => $section,
				'entry' => &$entry,
				'fields' => $fields
			));

			if(isset($this->_context['flag'])) {
				$new_link = $base . 'new/';
				$filter_link = $base;

				list($flag, $field_id, $value) = preg_split('/:/i', $this->_context['flag'], 3);

				if(isset($_REQUEST['prepopulate'])){
					$new_link .= '?';
					$filter_link .= '?';
					foreach($_REQUEST['prepopulate'] as $field_id => $value) {
						$new_link .= "prepopulate[$field_id]=$value&amp;";
						$field_name = FieldManager::fetchHandleFromID($field_id);
						$filter_link .= "filter[$field_name]=$value&amp;";
					}
					$new_link = preg_replace("/&amp;$/", '', $new_link);
					$filter_link = preg_replace("/&amp;$/", '', $filter_link);
				}

				// These flags are only relevant if there are no errors
				if(empty($this->_errors)) {
					switch($flag){
						case 'saved':
							$this->pageAlert(
								__('Entry updated at %s.', array(DateTimeObj::getTimeAgo()))
								. ' <a href="' . SYMPHONY_URL . $new_link . '" accesskey="c">'
								. __('Create another?')
								. '</a> <a href="' . SYMPHONY_URL . $filter_link . '" accesskey="a">'
								. __('View all Entries')
								. '</a>'
								, Alert::SUCCESS);
							break;

						case 'created':
							$this->pageAlert(
								__('Entry created at %s.', array(DateTimeObj::getTimeAgo()))
								. ' <a href="' . SYMPHONY_URL . $new_link . '" accesskey="c">'
								. __('Create another?')
								. '</a> <a href="' . SYMPHONY_URL . $filter_link . '" accesskey="a">'
								. __('View all Entries')
								. '</a>'
								, Alert::SUCCESS);
							break;
					}
				}
			}

			// Determine the page title
			$field_id = Symphony::Database()->fetchVar('id', 0, "SELECT `id` FROM `tbl_fields` WHERE `parent_section` = '".$section->get('id')."' ORDER BY `sortorder` LIMIT 1");
			$field = FieldManager::fetch($field_id);

			$title = trim(strip_tags($field->prepareTableValue($existingEntry->getData($field->get('id')), NULL, $entry_id)));

			if (trim($title) == '') {
				$title = __('Untitled');
			}

			// Check if there is a field to prepopulate
			if (isset($_REQUEST['prepopulate'])) {
				foreach($_REQUEST['prepopulate'] as $field_id => $value) {
					$this->Form->prependChild(Widget::Input(
						"prepopulate[{$field_id}]",
						rawurlencode($value),
						'hidden'
					));
				}
			}

			$this->setPageType('form');
			$this->Form->setAttribute('enctype', 'multipart/form-data');
			$this->Form->setAttribute('class', 'two columns');
			$this->setTitle(__('%1$s &ndash; %2$s &ndash; %3$s', array($title, $section->get('name'), __('Symphony'))));

			// Only show the Edit Section button if the Author is a developer. #938 ^BA
			if(Administration::instance()->Author->isDeveloper()) {
				$this->appendSubheading($title,
					Widget::Anchor(__('Edit Section'), SYMPHONY_URL . '/blueprints/sections/edit/' . $section_id, __('Edit Section Configuration'), 'button')
				);
			}
			else {
				$this->appendSubheading($title);
			}

			$this->insertBreadcrumbs(array(
				Widget::Anchor($section->get('name'), SYMPHONY_URL . (isset($filter_link) ? $filter_link : $base)),
			));

			$this->Form->appendChild(Widget::Input('MAX_FILE_SIZE', Symphony::Configuration()->get('max_upload_size', 'admin'), 'hidden'));

			$primary = new XMLElement('fieldset');
			$primary->setAttribute('class', 'primary column');

			$sidebar_fields = $section->fetchFields(NULL, 'sidebar');
			$main_fields = $section->fetchFields(NULL, 'main');

			if((!is_array($main_fields) || empty($main_fields)) && (!is_array($sidebar_fields) || empty($sidebar_fields))){
				$message = __('Fields must be added to this section before an entry can be created.');

				if(Administration::instance()->Author->isDeveloper()) {
					$message .= ' <a href="' . SYMPHONY_URL . '/blueprints/sections/edit/' . $section->get('id') . '/" accesskey="c">'
					. __('Add fields')
					. '</a>';
				}

				$this->pageAlert($message, Alert::ERROR);
			}

			else {

				if(is_array($main_fields) && !empty($main_fields)){
					foreach($main_fields as $field){
						$primary->appendChild($this->__wrapFieldWithDiv($field, $entry));
					}

					$this->Form->appendChild($primary);
				}

				if(is_array($sidebar_fields) && !empty($sidebar_fields)){
					$sidebar = new XMLElement('fieldset');
					$sidebar->setAttribute('class', 'secondary column');

					foreach($sidebar_fields as $field){
						$sidebar->appendChild($this->__wrapFieldWithDiv($field, $entry));
					}

					$this->Form->appendChild($sidebar);
				}

				$div = new XMLElement('div');
				$div->setAttribute('class', 'actions');
				$div->appendChild(Widget::Input('action[save]', __('Save Changes'), 'submit', array('accesskey' => 's')));

				$button = new XMLElement('button', __('Delete'));
				$button->setAttributeArray(array('name' => 'action[delete]', 'class' => 'button confirm delete', 'title' => __('Delete this entry'), 'type' => 'submit', 'accesskey' => 'd', 'data-message' => __('Are you sure you want to delete this entry?')));
				$div->appendChild($button);

				$this->Form->appendChild($div);
			}
		}

		public function __actionEdit(){

			$entry_id = intval($this->_context['entry_id']);

			if(@array_key_exists('save', $_POST['action']) || @array_key_exists("done", $_POST['action'])){
				if(!$ret = EntryManager::fetch($entry_id)) {
					Administration::instance()->customError(__('Unknown Entry'), __('The Entry, %s, could not be found.', array($entry_id)));
				}
				$entry = $ret[0];

				$section = SectionManager::fetch($entry->get('section_id'));

				$post = General::getPostData();
				$fields = $post['fields'];

				// Initial checks to see if the Entry is ok
				if(__ENTRY_FIELD_ERROR__ == $entry->checkPostData($fields, $this->_errors)) {
					$this->pageAlert(__('Some errors were encountered while attempting to save.'), Alert::ERROR);
				}

				// Secondary checks, this will actually process the data and attempt to save
				else if(__ENTRY_OK__ != $entry->setDataFromPost($fields, $errors)) {
					foreach($errors as $field_id => $message) {
						$this->pageAlert($message, Alert::ERROR);
					}
				}

				// Everything is awesome. Dance.
				else {
					/**
					 * Just prior to editing of an Entry.
					 *
					 * @delegate EntryPreEdit
					 * @param string $context
					 * '/publish/edit/'
					 * @param Section $section
					 * @param Entry $entry
					 * @param array $fields
					 */
					Symphony::ExtensionManager()->notifyMembers('EntryPreEdit', '/publish/edit/', array('section' => $section, 'entry' => &$entry, 'fields' => $fields));

					// Check to see if the dancing was premature
					if(!$entry->commit()){
						define_safe('__SYM_DB_INSERT_FAILED__', true);
						$this->pageAlert(NULL, Alert::ERROR);
					}

					else {
						/**
						 * Just after the editing of an Entry
						 *
						 * @delegate EntryPostEdit
						 * @param string $context
						 * '/publish/edit/'
						 * @param Section $section
						 * @param Entry $entry
						 * @param array $fields
						 */
						Symphony::ExtensionManager()->notifyMembers('EntryPostEdit', '/publish/edit/', array('section' => $section, 'entry' => $entry, 'fields' => $fields));

						$prepopulate_querystring = '';
						if(isset($_POST['prepopulate'])){
							foreach($_POST['prepopulate'] as $field_id => $value) {
								$prepopulate_querystring .= sprintf("prepopulate[%s]=%s&", $field_id, $value);
							}
							$prepopulate_querystring = trim($prepopulate_querystring, '&');
						}

						redirect(sprintf(
							'%s/publish/%s/edit/%d/saved/%s',
							SYMPHONY_URL,
							$this->_context['section_handle'],
							$entry->get('id'),
							(!empty($prepopulate_querystring) ? "?" . $prepopulate_querystring : NULL)
						));
					}
				}
			}

			else if(@array_key_exists('delete', $_POST['action']) && is_numeric($entry_id)){
				/**
				 * Prior to deletion of entries. An array of Entry ID's is provided which
				 * can be manipulated. This delegate was renamed from `Delete` to `EntryPreDelete`
				 * in Symphony 2.3.
				 *
				 * @delegate EntryPreDelete
				 * @param string $context
				 * '/publish/'
				 * @param array $entry_id
				 *	An array of Entry ID's passed by reference
				 */
				$checked = array($entry_id);
				Symphony::ExtensionManager()->notifyMembers('EntryPreDelete', '/publish/', array('entry_id' => &$checked));

				EntryManager::delete($checked);

				/**
				 * After the deletion of entries, this delegate provides an array of Entry ID's
				 * that were deleted.
				 *
				 * @since Symphony 2.3
				 * @delegate EntryPostDelete
				 * @param string $context
				 * '/publish/'
				 * @param array $entry_id
				 *  An array of Entry ID's that were deleted.
				 */
				Symphony::ExtensionManager()->notifyMembers('EntryPostDelete', '/publish/', array('entry_id' => $checked));

				redirect(SYMPHONY_URL . '/publish/'.$this->_context['section_handle'].'/');
			}

		}

		/**
		 * Given a Field and Entry object, this function will wrap
		 * the Field's displayPublishPanel result with a div that
		 * contains some contextual information such as the Field ID,
		 * the Field handle and whether it is required or not.
		 *
		 * @param Field $field
		 * @param Entry $entry
		 * @return XMLElement
		 */
		private function __wrapFieldWithDiv(Field $field, Entry $entry){
			$div = new XMLElement('div', NULL, array('id' => 'field-' . $field->get('id'), 'class' => 'field field-'.$field->handle().($field->get('required') == 'yes' ? ' required' : '')));
			$field->displayPublishPanel(
				$div, $entry->getData($field->get('id')),
				(isset($this->_errors[$field->get('id')]) ? $this->_errors[$field->get('id')] : NULL),
				null, null, (is_numeric($entry->get('id')) ? $entry->get('id') : NULL)
			);
			return $div;
		}

	}

