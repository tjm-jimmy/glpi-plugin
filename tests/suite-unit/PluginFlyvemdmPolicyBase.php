<?php
/**
 * LICENSE
 *
 * Copyright © 2016-2018 Teclib'
 * Copyright © 2010-2018 by the FusionInventory Development Team.
 *
 * This file is part of Flyve MDM Plugin for GLPI.
 *
 * Flyve MDM Plugin for GLPI is a subproject of Flyve MDM. Flyve MDM is a mobile
 * device management software.
 *
 * Flyve MDM Plugin for GLPI is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Affero General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * Flyve MDM Plugin for GLPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 * You should have received a copy of the GNU Affero General Public License
 * along with Flyve MDM Plugin for GLPI. If not, see http://www.gnu.org/licenses/.
 * ------------------------------------------------------------------------------
 * @author    Domingo Oropeza
 * @copyright Copyright © 2018 Teclib
 * @license   AGPLv3+ http://www.gnu.org/licenses/agpl.txt
 * @link      https://github.com/flyve-mdm/glpi-plugin
 * @link      https://flyve-mdm.com/
 * ------------------------------------------------------------------------------
 */

namespace tests\units;

use Flyvemdm\Tests\CommonTestCase;

class PluginFlyvemdmPolicyBase extends CommonTestCase {

   private $dataField = [];

   /**
    * @return array
    */
   private function createNewPolicyInstance() {
      $policyData = new \PluginFlyvemdmPolicy();
      $policyData->fields = $this->dataField;
      $policy = $this->newTestedInstance($policyData);
      return [$policy, $policyData];
   }

   /**
    * @tags testCanApply
    */
   public function testCanApply() {
      list($policy) = $this->createNewPolicyInstance();
      $this->boolean($policy->canApply(null, null, null, new \PluginFlyvemdmFleet()))->isTrue();
   }

   /**
    * @tags testUnicityCheck
    */
   public function testUnicityCheck() {
      $this->dataField = ['id' => 1];
      list($policy) = $this->createNewPolicyInstance();

      $mockedFleet = $this->newMockInstance('\PluginFlyvemdmFleet');
      $mockedFleet->getMockController()->getID = 1;
      $this->boolean($policy->unicityCheck(null, null, null, $mockedFleet))->isTrue();
      // TODO this second call should return false
      //$this->boolean($policy->unicityCheck(null, null, null, $mockedFleet))->isFalse();
   }

   /**
    * @tags testConflictCheck
    */
   public function testConflictCheck() {
      list($policy) = $this->createNewPolicyInstance();
      $this->boolean($policy->conflictCheck(null, null, null,
         new \PluginFlyvemdmFleet()))->isTrue();
   }

   /**
    * @tags testIntegrityCheck
    */
   public function testIntegrityCheck() {
      list($policy) = $this->createNewPolicyInstance();
      $this->boolean($policy->integrityCheck(null, null, null))->isTrue();
   }

   /**
    * @tags testTranslateData
    */
   public function testTranslateData() {
      list($policy) = $this->createNewPolicyInstance();
      $this->string($policy->translateData())->isEmpty();
   }

   /**
    * @tags testGetGroup
    */
   public function testGetGroup() {
      list($policy) = $this->createNewPolicyInstance();
      $this->variable($policy->getGroup())->isNull();
   }

   /**
    * @tags testApply
    */
   public function testPre_apply() {
      list($policy) = $this->createNewPolicyInstance();
      $this->boolean($policy->pre_apply(null, null, null, new \PluginFlyvemdmFleet()))->isTrue();
   }

   /**
    * @tags testUnapply
    */
   public function testPre_unapply() {
      list($policy) = $this->createNewPolicyInstance();
      $this->boolean($policy->pre_unapply(null, null, null, new \PluginFlyvemdmFleet()))->isTrue();
   }

   /**
    * @tags testShowValueInput
    */
   public function testShowValueInput() {
      list($policy) = $this->createNewPolicyInstance();
      $data['itemtype'] = '';
      $data['value'] = '';
      $data['typeTmpl'] = \PluginFlyvemdmPolicyBase::class;
      $twig = plugin_flyvemdm_getTemplateEngine();
      $this->string($policy->showValueInput())->isEqualTo($twig->render('policy_value.html.twig', ['data' => $data]));
   }

   /**
    * @tags testShowValue
    */
   public function testShowValue() {
      list($policy) = $this->createNewPolicyInstance();
      $mockedFleet = $this->newMockInstance('\PluginFlyvemdmTask');
      $mockedFleet->getMockController()->getField = 'lorem';
      $this->string($policy->showValue($mockedFleet))->isEqualTo('lorem');
   }

   /**
    * @tags testPreprocessFormData
    */
   public function testPreprocessFormData() {
      list($policy) = $this->createNewPolicyInstance();
      $this->array($policy->preprocessFormData($input = ['field' => 'value']))->isEqualTo($input);
   }

   /**
    * @tags testFilterStatus
    */
   public function testFilterStatus() {
      list($policy) = $this->createNewPolicyInstance();
      $this->string($policy->filterStatus($status = 'done'))->isEqualTo($status);
      $this->variable($policy->filterStatus($status = 'invalid status'))->isNull();
   }

   /**
    * @tags testGetPolicyData
    */
   public function testGetPolicyData() {
      list($policy) = $this->createNewPolicyInstance();
      $this->object($policy->getPolicyData())->isInstanceOf('PluginFlyvemdmPolicy');
   }

   /**
    * @tags testTranslateData
    */
   public function testGetEnumBaseTaskStatus() {
      $expectedStatuses = [
         'pending',
         'received',
         'done',
         'failed',
         'canceled',
         'incompatible',
         'overriden',
      ];

      $statuses = \PluginFlyvemdmPolicyBase::getEnumBaseTaskStatus();
      $this->array($statuses)->hasKeys($expectedStatuses);
      $this->array($statuses)->size->isEqualTo(count($expectedStatuses));
   }

   /**
    * Used in other test classes of policies
    */
   public function providerFilterStatus() {
      $statuses = \PluginFlyvemdmPolicyBase::getEnumBaseTaskStatus();
      $providedStatuses = [];
      foreach ($statuses as $status => $localized) {
         $providedStatuses[] = [
            'status'   => $status,
            'expected' => $status
         ];
      }

      return $providedStatuses;
   }

   /**
    * @tags testFormGenerator
    */
   public function testFormGenerator() {
      list($policy) = $this->createNewPolicyInstance();
      // add action
      $html = $policy->formGenerator('add', [
         'policyId'         => 1,
         'task'             => 0,
         'itemtype_applied' => '',
         'items_id_applied' => 0,
      ]);
      $this->string($html)
         ->notContains('<form name="policy_editor_form')
         ->contains('input name="value" value=""');

      // edit action
      $html = $policy->formGenerator('update', [
         'policyId'         => 1,
         'task'             => 5,
         'itemtype_applied' => 'fleet',
         'items_id_applied' => 10,
      ]);
      $formAction = preg_quote("/plugins/flyvemdm/front/task.form.php", '/');
      $this->string($html)
         ->matches('#action=".+?' . $formAction . '"#')
         ->contains('input name="value" value="N/A"')
         ->contains('input type="hidden" name="_glpi_csrf_token"')
         ->contains("input type='hidden' name='id' value='5'")
         ->contains("input type='hidden' name='plugin_flyvemdm_policies_id' value='1'")
         ->contains("input type='hidden' name='itemtype_applied' value='fleet'")
         ->contains("input type='hidden' name='items_id_applied' value='10'");
   }
}
