<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * API tests.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

use tool_lp\api;

/**
 * API tests.
 *
 * @package    tool_lp
 * @copyright  2015 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_lp_api_testcase extends advanced_testcase {

    public function test_get_framework_related_contexts() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $cat1 = $dg->create_category();
        $cat2 = $dg->create_category(array('parent' => $cat1->id));
        $cat3 = $dg->create_category(array('parent' => $cat2->id));
        $c1 = $dg->create_course(array('category' => $cat2->id));   // This context should not be returned.

        $cat1ctx = context_coursecat::instance($cat1->id);
        $cat2ctx = context_coursecat::instance($cat2->id);
        $cat3ctx = context_coursecat::instance($cat3->id);
        $sysctx = context_system::instance();

        $expected = array($cat1ctx->id => $cat1ctx);
        $this->assertEquals($expected, api::get_related_contexts($cat1ctx, 'self'));

        $expected = array($cat1ctx->id => $cat1ctx, $cat2ctx->id => $cat2ctx, $cat3ctx->id => $cat3ctx);
        $this->assertEquals($expected, api::get_related_contexts($cat1ctx, 'children'));

        $expected = array($sysctx->id => $sysctx, $cat1ctx->id => $cat1ctx, $cat2ctx->id => $cat2ctx);
        $this->assertEquals($expected, api::get_related_contexts($cat2ctx, 'parents'));
    }

    public function test_get_framework_related_contexts_with_capabilities() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $user = $dg->create_user();
        $cat1 = $dg->create_category();
        $cat2 = $dg->create_category(array('parent' => $cat1->id));
        $cat3 = $dg->create_category(array('parent' => $cat2->id));
        $c1 = $dg->create_course(array('category' => $cat2->id));   // This context should not be returned.

        $cat1ctx = context_coursecat::instance($cat1->id);
        $cat2ctx = context_coursecat::instance($cat2->id);
        $cat3ctx = context_coursecat::instance($cat3->id);
        $sysctx = context_system::instance();

        $roleallow = create_role('Allow', 'allow', 'Allow read');
        assign_capability('tool/lp:competencyread', CAP_ALLOW, $roleallow, $sysctx->id);
        role_assign($roleallow, $user->id, $sysctx->id);

        $roleprevent = create_role('Prevent', 'prevent', 'Prevent read');
        assign_capability('tool/lp:competencyread', CAP_PROHIBIT, $roleprevent, $sysctx->id);
        role_assign($roleprevent, $user->id, $cat2ctx->id);

        accesslib_clear_all_caches_for_unit_testing();
        $this->setUser($user);
        $this->assertFalse(has_capability('tool/lp:competencyread', $cat2ctx));

        $requiredcap = array('tool/lp:competencyread');

        $expected = array();
        $this->assertEquals($expected, api::get_related_contexts($cat2ctx, 'self', $requiredcap));

        $expected = array($cat1ctx->id => $cat1ctx);
        $this->assertEquals($expected, api::get_related_contexts($cat1ctx, 'children', $requiredcap));

        $expected = array($sysctx->id => $sysctx, $cat1ctx->id => $cat1ctx);
        $this->assertEquals($expected, api::get_related_contexts($cat2ctx, 'parents', $requiredcap));
    }

    public function test_get_template_related_contexts() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $cat1 = $dg->create_category();
        $cat2 = $dg->create_category(array('parent' => $cat1->id));
        $cat3 = $dg->create_category(array('parent' => $cat2->id));
        $c1 = $dg->create_course(array('category' => $cat2->id));   // This context should not be returned.

        $cat1ctx = context_coursecat::instance($cat1->id);
        $cat2ctx = context_coursecat::instance($cat2->id);
        $cat3ctx = context_coursecat::instance($cat3->id);
        $sysctx = context_system::instance();

        $expected = array($cat1ctx->id => $cat1ctx);
        $this->assertEquals($expected, api::get_related_contexts($cat1ctx, 'self'));

        $expected = array($cat1ctx->id => $cat1ctx, $cat2ctx->id => $cat2ctx, $cat3ctx->id => $cat3ctx);
        $this->assertEquals($expected, api::get_related_contexts($cat1ctx, 'children'));

        $expected = array($sysctx->id => $sysctx, $cat1ctx->id => $cat1ctx, $cat2ctx->id => $cat2ctx);
        $this->assertEquals($expected, api::get_related_contexts($cat2ctx, 'parents'));
    }

    public function test_get_template_related_contexts_with_capabilities() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $user = $dg->create_user();
        $cat1 = $dg->create_category();
        $cat2 = $dg->create_category(array('parent' => $cat1->id));
        $cat3 = $dg->create_category(array('parent' => $cat2->id));
        $c1 = $dg->create_course(array('category' => $cat2->id));   // This context should not be returned.

        $cat1ctx = context_coursecat::instance($cat1->id);
        $cat2ctx = context_coursecat::instance($cat2->id);
        $cat3ctx = context_coursecat::instance($cat3->id);
        $sysctx = context_system::instance();

        $roleallow = create_role('Allow', 'allow', 'Allow read');
        assign_capability('tool/lp:templateread', CAP_ALLOW, $roleallow, $sysctx->id);
        role_assign($roleallow, $user->id, $sysctx->id);

        $roleprevent = create_role('Prevent', 'prevent', 'Prevent read');
        assign_capability('tool/lp:templateread', CAP_PROHIBIT, $roleprevent, $sysctx->id);
        role_assign($roleprevent, $user->id, $cat2ctx->id);

        accesslib_clear_all_caches_for_unit_testing();
        $this->setUser($user);
        $this->assertFalse(has_capability('tool/lp:templateread', $cat2ctx));

        $requiredcap = array('tool/lp:templateread');

        $expected = array();
        $this->assertEquals($expected, api::get_related_contexts($cat2ctx, 'self', $requiredcap));

        $expected = array($cat1ctx->id => $cat1ctx);
        $this->assertEquals($expected, api::get_related_contexts($cat1ctx, 'children', $requiredcap));

        $expected = array($sysctx->id => $sysctx, $cat1ctx->id => $cat1ctx);
        $this->assertEquals($expected, api::get_related_contexts($cat2ctx, 'parents', $requiredcap));
    }

    /**
     * Test updating a template.
     */
    public function test_update_template() {
        $cat = $this->getDataGenerator()->create_category();
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $syscontext = context_system::instance();
        $template = api::create_template((object) array('shortname' => 'testing', 'contextid' => $syscontext->id));

        $this->assertEquals('testing', $template->get_shortname());
        $this->assertEquals($syscontext->id, $template->get_contextid());

        // Simple update.
        api::update_template((object) array('id' => $template->get_id(), 'shortname' => 'success'));
        $template = api::read_template($template->get_id());
        $this->assertEquals('success', $template->get_shortname());

        // Trying to change the context.
        $this->setExpectedException('coding_exception');
        api::update_template((object) array('id' => $template->get_id(), 'contextid' => context_coursecat::instance($cat->id)));
    }

    /**
     * Test listing framework with order param.
     */
    public function test_list_frameworks() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');

        // Create a list of frameworks.
        $framework1 = $lpg->create_framework(array(
            'shortname' => 'shortname_a',
            'idnumber' => 'idnumber_c',
            'description' => 'description',
            'descriptionformat' => FORMAT_HTML,
            'visible' => true,
            'contextid' => context_system::instance()->id
        ));

        $framework2 = $lpg->create_framework(array(
            'shortname' => 'shortname_b',
            'idnumber' => 'idnumber_a',
            'description' => 'description',
            'descriptionformat' => FORMAT_HTML,
            'visible' => true,
            'contextid' => context_system::instance()->id
        ));

        $framework3 = $lpg->create_framework(array(
            'shortname' => 'shortname_c',
            'idnumber' => 'idnumber_b',
            'description' => 'description',
            'descriptionformat' => FORMAT_HTML,
            'visible' => true,
            'contextid' => context_system::instance()->id
        ));

        // Get frameworks list order by shortname desc.
        $result = api::list_frameworks('shortname', 'DESC', null, 3, context_system::instance());

        $f = (object) array_shift($result);
        $this->assertEquals($framework3->get_id(), $f->get_id());
        $f = (object) array_shift($result);
        $this->assertEquals($framework2->get_id(), $f->get_id());
        $f = (object) array_shift($result);
        $this->assertEquals($framework1->get_id(), $f->get_id());

        // Get frameworks list order by idnumber asc.
        $result = api::list_frameworks('idnumber', 'ASC', null, 3, context_system::instance());

        $f = (object) array_shift($result);
        $this->assertEquals($framework2->get_id(), $f->get_id());
        $f = (object) array_shift($result);
        $this->assertEquals($framework3->get_id(), $f->get_id());
        $f = (object) array_shift($result);
        $this->assertEquals($framework1->get_id(), $f->get_id());
    }

    /**
     * Test duplicate a framework.
     */
    public function test_duplicate_framework() {
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $syscontext = context_system::instance();
        $params = array(
                'shortname' => 'shortname_a',
                'idnumber' => 'idnumber_c',
                'description' => 'description',
                'descriptionformat' => FORMAT_HTML,
                'visible' => true,
                'contextid' => $syscontext->id
        );
        $framework = $lpg->create_framework($params);
        $competency1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $competency2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $competency3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $competency4 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $competencyidnumbers = array($competency1->get_idnumber(),
                                        $competency2->get_idnumber(),
                                        $competency3->get_idnumber(),
                                        $competency4->get_idnumber()
                                    );

        api::add_related_competency($competency1->get_id(), $competency2->get_id());
        api::add_related_competency($competency3->get_id(), $competency4->get_id());

        $frameworkduplicated1 = api::duplicate_framework($framework->get_id());
        $frameworkduplicated2 = api::duplicate_framework($framework->get_id());

        $this->assertEquals($framework->get_idnumber().'_1', $frameworkduplicated1->get_idnumber());
        $this->assertEquals($framework->get_idnumber().'_2', $frameworkduplicated2->get_idnumber());

        $competenciesfr1 = api::list_competencies(array('competencyframeworkid' => $frameworkduplicated1->get_id()));
        $competenciesfr2 = api::list_competencies(array('competencyframeworkid' => $frameworkduplicated2->get_id()));

        $competencyidsfr1 = array();
        $competencyidsfr2 = array();

        foreach ($competenciesfr1 as $cmp) {
            $competencyidsfr1[] = $cmp->get_idnumber();
        }
        foreach ($competenciesfr2 as $cmp) {
            $competencyidsfr2[] = $cmp->get_idnumber();
        }

        $this->assertEmpty(array_diff($competencyidsfr1, $competencyidnumbers));
        $this->assertEmpty(array_diff($competencyidsfr2, $competencyidnumbers));
        $this->assertCount(4, $competenciesfr1);
        $this->assertCount(4, $competenciesfr2);

        // Test the related competencies.
        reset($competenciesfr1);
        $compduplicated1 = current($competenciesfr1);
        $relatedcompetencies = $compduplicated1->get_related_competencies();
        $comprelated = current($relatedcompetencies);
        $this->assertEquals($comprelated->get_idnumber(), $competency2->get_idnumber());
    }

    /**
     * Test update plan.
     */
    public function test_update_plan() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $usermanageowndraft = $dg->create_user();
        $usermanageown = $dg->create_user();
        $usermanagedraft = $dg->create_user();
        $usermanage = $dg->create_user();

        $syscontext = context_system::instance();

        // Creating specific roles.
        $manageowndraftrole = $dg->create_role(array(
            'name' => 'User manage own draft',
            'shortname' => 'manage-own-draft'
        ));
        $manageownrole = $dg->create_role(array(
            'name' => 'User manage own',
            'shortname' => 'manage-own'
        ));
        $managedraftrole = $dg->create_role(array(
            'name' => 'User manage draft',
            'shortname' => 'manage-draft'
        ));
        $managerole = $dg->create_role(array(
            'name' => 'User manage',
            'shortname' => 'manage'
        ));

        assign_capability('tool/lp:planmanageowndraft', CAP_ALLOW, $manageowndraftrole, $syscontext->id);
        assign_capability('tool/lp:planviewowndraft', CAP_ALLOW, $manageowndraftrole, $syscontext->id);

        assign_capability('tool/lp:planmanageown', CAP_ALLOW, $manageownrole, $syscontext->id);
        assign_capability('tool/lp:planviewown', CAP_ALLOW, $manageownrole, $syscontext->id);

        assign_capability('tool/lp:planmanagedraft', CAP_ALLOW, $managedraftrole, $syscontext->id);
        assign_capability('tool/lp:planviewdraft', CAP_ALLOW, $managedraftrole, $syscontext->id);

        assign_capability('tool/lp:planmanage', CAP_ALLOW, $managerole, $syscontext->id);
        assign_capability('tool/lp:planview', CAP_ALLOW, $managerole, $syscontext->id);

        $dg->role_assign($manageowndraftrole, $usermanageowndraft->id, $syscontext->id);
        $dg->role_assign($manageownrole, $usermanageown->id, $syscontext->id);
        $dg->role_assign($managedraftrole, $usermanagedraft->id, $syscontext->id);
        $dg->role_assign($managerole, $usermanage->id, $syscontext->id);

        // Create first learning plan with user create draft.
        $this->setUser($usermanageowndraft);
        $plan = array (
            'name' => 'plan own draft',
            'description' => 'plan own draft',
            'userid' => $usermanageowndraft->id
        );
        $plan = api::create_plan((object)$plan);
        $record = $plan->to_record();
        $record->name = 'plan own draft modified';

        // Check if user create draft can edit the plan name.
        $plan = api::update_plan($record);
        $this->assertInstanceOf('\tool_lp\plan', $plan);

        // Thrown exception when manageowndraft user try to change the status.
        $record->status = \tool_lp\plan::STATUS_ACTIVE;
        try {
            $plan = api::update_plan($record);
            $this->fail('User with manage own draft capability cannot edit the plan status.');
        } catch (required_capability_exception $e) {
            $this->assertTrue(true);
        }

        // Test when user with manage own plan capability try to edit other user plan.
        $record->status = \tool_lp\plan::STATUS_DRAFT;
        $record->name = 'plan create draft modified 2';
        $this->setUser($usermanageown);
        try {
            $plan = api::update_plan($record);
            $this->fail('User with manage own plan capability can only edit his own plan.');
        } catch (required_capability_exception $e) {
            $this->assertTrue(true);
        }

        // User with manage plan capability cannot edit the other user plans with status draft.
        $this->setUser($usermanage);
        $record->status = \tool_lp\plan::STATUS_COMPLETE;
        try {
            $plan = api::update_plan($record);
            $this->fail('User with manage plan capability cannot edit the other user plans with status draft');
        } catch (required_capability_exception $e) {
            $this->assertTrue(true);
        }

        // User with manage draft capability can edit other user's learning plan if the status is draft.
        $this->setUser($usermanagedraft);
        $record->status = \tool_lp\plan::STATUS_DRAFT;
        $record->name = 'plan manage draft modified 3';
        $plan = api::update_plan($record);
        $this->assertInstanceOf('\tool_lp\plan', $plan);

        // User with manage  plan capability can create/edit learning plan if status is active/complete.
        $this->setUser($usermanage);
        $plan = array (
            'name' => 'plan create',
            'description' => 'plan create',
            'userid' => $usermanage->id,
            'status' => \tool_lp\plan::STATUS_ACTIVE
        );
        $plan = api::create_plan((object)$plan);

        // Silently transition to complete status to avoid errors about transitioning to complete.
        $plan->set_status(\tool_lp\plan::STATUS_COMPLETE);
        $plan->update();

        $record = $plan->to_record();
        $record->name = 'plan create own modified';
        try {
            api::update_plan($record);
            $this->fail('Completed plan can not be edited');
        } catch (coding_exception $e) {
            $this->assertTrue(true);
        }
    }

    public function test_create_plan_from_template() {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $u1 = $this->getDataGenerator()->create_user();
        $tpl = $this->getDataGenerator()->get_plugin_generator('tool_lp')->create_template();

        // Creating a new plan.
        $plan = api::create_plan_from_template($tpl, $u1->id);
        $record = $plan->to_record();
        $this->assertInstanceOf('\tool_lp\plan', $plan);
        $this->assertTrue(\tool_lp\plan::record_exists($plan->get_id()));
        $this->assertEquals($tpl->get_id(), $plan->get_templateid());
        $this->assertEquals($u1->id, $plan->get_userid());
        $this->assertTrue($plan->is_based_on_template());

        // Creating a plan that already exists.
        $plan = api::create_plan_from_template($tpl, $u1->id);
        $this->assertFalse($plan);

        // Check that api::create_plan cannot be used.
        $this->setExpectedException('coding_exception');
        unset($record->id);
        $plan = api::create_plan($record);
    }

    public function test_update_plan_based_on_template() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('tool_lp');
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();

        $this->setAdminUser();
        $tpl1 = $lpg->create_template();
        $tpl2 = $lpg->create_template();
        $up1 = $lpg->create_plan(array('userid' => $u1->id, 'templateid' => $tpl1->get_id()));
        $up2 = $lpg->create_plan(array('userid' => $u2->id, 'templateid' => null));

        try {
            // Trying to remove the template dependency.
            $record = $up1->to_record();
            $record->templateid = null;
            api::update_plan($record);
            $this->fail('A plan cannot be unlinked using api::update_plan()');
        } catch (coding_exception $e) {
        }

        try {
            // Trying to switch to another template.
            $record = $up1->to_record();
            $record->templateid = $tpl2->get_id();
            api::update_plan($record);
            $this->fail('A plan cannot be moved to another template.');
        } catch (coding_exception $e) {
        }

        try {
            // Trying to switch to using a template.
            $record = $up2->to_record();
            $record->templateid = $tpl1->get_id();
            api::update_plan($record);
            $this->fail('A plan cannot be update to use a template.');
        } catch (coding_exception $e) {
        }
    }

    public function test_unlink_plan_from_template() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('tool_lp');
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();

        $this->setAdminUser();
        $f1 = $lpg->create_framework();
        $f2 = $lpg->create_framework();
        $c1a = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));
        $c2a = $lpg->create_competency(array('competencyframeworkid' => $f2->get_id()));
        $c1b = $lpg->create_competency(array('competencyframeworkid' => $f1->get_id()));

        $tpl1 = $lpg->create_template();
        $tpl2 = $lpg->create_template();

        $tplc1a = $lpg->create_template_competency(array('templateid' => $tpl1->get_id(), 'competencyid' => $c1a->get_id(),
            'sortorder' => 9));
        $tplc1b = $lpg->create_template_competency(array('templateid' => $tpl1->get_id(), 'competencyid' => $c1b->get_id(),
            'sortorder' => 8));
        $tplc2a = $lpg->create_template_competency(array('templateid' => $tpl2->get_id(), 'competencyid' => $c2a->get_id()));

        $plan1 = $lpg->create_plan(array('userid' => $u1->id, 'templateid' => $tpl1->get_id()));
        $plan2 = $lpg->create_plan(array('userid' => $u2->id, 'templateid' => $tpl2->get_id()));

        // Check that we have what we expect at this stage.
        $this->assertEquals(2, \tool_lp\template_competency::count_records(array('templateid' => $tpl1->get_id())));
        $this->assertEquals(1, \tool_lp\template_competency::count_records(array('templateid' => $tpl2->get_id())));
        $this->assertEquals(0, \tool_lp\plan_competency::count_records(array('planid' => $plan1->get_id())));
        $this->assertEquals(0, \tool_lp\plan_competency::count_records(array('planid' => $plan2->get_id())));
        $this->assertTrue($plan1->is_based_on_template());
        $this->assertTrue($plan2->is_based_on_template());

        // Let's do this!
        $tpl1comps = \tool_lp\template_competency::list_competencies($tpl1->get_id(), true);
        $tpl2comps = \tool_lp\template_competency::list_competencies($tpl2->get_id(), true);

        api::unlink_plan_from_template($plan1);

        $plan1->read();
        $plan2->read();
        $this->assertCount(2, $tpl1comps);
        $this->assertCount(1, $tpl2comps);
        $this->assertEquals(2, \tool_lp\template_competency::count_records(array('templateid' => $tpl1->get_id())));
        $this->assertEquals(1, \tool_lp\template_competency::count_records(array('templateid' => $tpl2->get_id())));
        $this->assertEquals(2, \tool_lp\plan_competency::count_records(array('planid' => $plan1->get_id())));
        $this->assertEquals(0, \tool_lp\plan_competency::count_records(array('planid' => $plan2->get_id())));
        $this->assertFalse($plan1->is_based_on_template());
        $this->assertEquals($tpl1->get_id(), $plan1->get_origtemplateid());
        $this->assertTrue($plan2->is_based_on_template());
        $this->assertEquals(null, $plan2->get_origtemplateid());

        // Even the order remains.
        $plan1comps = \tool_lp\plan_competency::list_competencies($plan1->get_id());
        $before = reset($tpl1comps);
        $after = reset($plan1comps);
        $this->assertEquals($before->get_id(), $after->get_id());
        $this->assertEquals($before->get_sortorder(), $after->get_sortorder());
        $before = next($tpl1comps);
        $after = next($plan1comps);
        $this->assertEquals($before->get_id(), $after->get_id());
        $this->assertEquals($before->get_sortorder(), $after->get_sortorder());
    }

    public function test_update_template_updates_plans() {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $dg = $this->getDataGenerator();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $lpg = $dg->get_plugin_generator('tool_lp');
        $tpl1 = $lpg->create_template();
        $tpl2 = $lpg->create_template();


        // Create plans with data not matching templates.
        $time = time();
        $plan1 = $lpg->create_plan(array('templateid' => $tpl1->get_id(), 'userid' => $u1->id,
            'name' => 'Not good name', 'duedate' => $time + 3600, 'description' => 'Ahah', 'descriptionformat' => FORMAT_MARKDOWN));
        $plan2 = $lpg->create_plan(array('templateid' => $tpl1->get_id(), 'userid' => $u2->id,
            'name' => 'Not right name', 'duedate' => $time + 3601, 'description' => 'Ahah', 'descriptionformat' => FORMAT_PLAIN));
        $plan3 = $lpg->create_plan(array('templateid' => $tpl2->get_id(), 'userid' => $u1->id,
            'name' => 'Not sweet name', 'duedate' => $time + 3602, 'description' => 'Ahah', 'descriptionformat' => FORMAT_PLAIN));

        // Prepare our expectations.
        $plan1->read();
        $plan2->read();
        $plan3->read();

        $this->assertEquals($tpl1->get_id(), $plan1->get_templateid());
        $this->assertEquals($tpl1->get_id(), $plan2->get_templateid());
        $this->assertEquals($tpl2->get_id(), $plan3->get_templateid());
        $this->assertNotEquals($tpl1->get_shortname(), $plan1->get_name());
        $this->assertNotEquals($tpl1->get_shortname(), $plan2->get_name());
        $this->assertNotEquals($tpl2->get_shortname(), $plan3->get_name());
        $this->assertNotEquals($tpl1->get_description(), $plan1->get_description());
        $this->assertNotEquals($tpl1->get_description(), $plan2->get_description());
        $this->assertNotEquals($tpl2->get_description(), $plan3->get_description());
        $this->assertNotEquals($tpl1->get_descriptionformat(), $plan1->get_descriptionformat());
        $this->assertNotEquals($tpl1->get_descriptionformat(), $plan2->get_descriptionformat());
        $this->assertNotEquals($tpl2->get_descriptionformat(), $plan3->get_descriptionformat());
        $this->assertNotEquals($tpl1->get_duedate(), $plan1->get_duedate());
        $this->assertNotEquals($tpl1->get_duedate(), $plan2->get_duedate());
        $this->assertNotEquals($tpl2->get_duedate(), $plan3->get_duedate());

        // Update the template without changing critical fields does not update the plans.
        $data = $tpl1->to_record();
        $data->visible = 0;
        api::update_template($data);
        $this->assertNotEquals($tpl1->get_shortname(), $plan1->get_name());
        $this->assertNotEquals($tpl1->get_shortname(), $plan2->get_name());
        $this->assertNotEquals($tpl2->get_shortname(), $plan3->get_name());
        $this->assertNotEquals($tpl1->get_description(), $plan1->get_description());
        $this->assertNotEquals($tpl1->get_description(), $plan2->get_description());
        $this->assertNotEquals($tpl2->get_description(), $plan3->get_description());
        $this->assertNotEquals($tpl1->get_descriptionformat(), $plan1->get_descriptionformat());
        $this->assertNotEquals($tpl1->get_descriptionformat(), $plan2->get_descriptionformat());
        $this->assertNotEquals($tpl2->get_descriptionformat(), $plan3->get_descriptionformat());
        $this->assertNotEquals($tpl1->get_duedate(), $plan1->get_duedate());
        $this->assertNotEquals($tpl1->get_duedate(), $plan2->get_duedate());
        $this->assertNotEquals($tpl2->get_duedate(), $plan3->get_duedate());

        // Now really update the template.
        $data = $tpl1->to_record();
        $data->shortname = 'Awesome!';
        $data->description = 'This is too awesome!';
        $data->descriptionformat = FORMAT_HTML;
        $data->duedate = $time + 7200;
        api::update_template($data);
        $tpl1->read();

        // Now confirm that the right plans were updated.
        $plan1->read();
        $plan2->read();
        $plan3->read();

        $this->assertEquals($tpl1->get_id(), $plan1->get_templateid());
        $this->assertEquals($tpl1->get_id(), $plan2->get_templateid());
        $this->assertEquals($tpl2->get_id(), $plan3->get_templateid());

        $this->assertEquals($tpl1->get_shortname(), $plan1->get_name());
        $this->assertEquals($tpl1->get_shortname(), $plan2->get_name());
        $this->assertNotEquals($tpl2->get_shortname(), $plan3->get_name());
        $this->assertEquals($tpl1->get_description(), $plan1->get_description());
        $this->assertEquals($tpl1->get_description(), $plan2->get_description());
        $this->assertNotEquals($tpl2->get_description(), $plan3->get_description());
        $this->assertEquals($tpl1->get_descriptionformat(), $plan1->get_descriptionformat());
        $this->assertEquals($tpl1->get_descriptionformat(), $plan2->get_descriptionformat());
        $this->assertNotEquals($tpl2->get_descriptionformat(), $plan3->get_descriptionformat());
        $this->assertEquals($tpl1->get_duedate(), $plan1->get_duedate());
        $this->assertEquals($tpl1->get_duedate(), $plan2->get_duedate());
        $this->assertNotEquals($tpl2->get_duedate(), $plan3->get_duedate());
    }

    /**
     * Test that the method to complete a plan.
     */
    public function test_complete_plan() {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminUser();
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');
        $user = $dg->create_user();

        // Create a framework and assign competencies.
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));

        // Create two plans and assign competencies.
        $plan = $lpg->create_plan(array('userid' => $user->id));
        $otherplan = $lpg->create_plan(array('userid' => $user->id));

        $lpg->create_plan_competency(array('planid' => $plan->get_id(), 'competencyid' => $c1->get_id()));
        $lpg->create_plan_competency(array('planid' => $plan->get_id(), 'competencyid' => $c2->get_id()));
        $lpg->create_plan_competency(array('planid' => $plan->get_id(), 'competencyid' => $c3->get_id()));
        $lpg->create_plan_competency(array('planid' => $otherplan->get_id(), 'competencyid' => $c1->get_id()));

        $uclist = array(
            $lpg->create_user_competency(array('userid' => $user->id, 'competencyid' => $c1->get_id(),
                'proficiency' => true, 'grade' => 1 )),
            $lpg->create_user_competency(array('userid' => $user->id, 'competencyid' => $c2->get_id(),
                'proficiency' => false, 'grade' => 2 ))
        );

        $this->assertEquals(2, \tool_lp\user_competency::count_records());
        $this->assertEquals(0, \tool_lp\user_competency_plan::count_records());

        // Change status of the plan to complete.
        api::complete_plan($plan);

        // Check that user competencies are now in user_competency_plan objects and still in user_competency.
        $this->assertEquals(2, \tool_lp\user_competency::count_records());
        $this->assertEquals(3, \tool_lp\user_competency_plan::count_records());

        $usercompetenciesplan = \tool_lp\user_competency_plan::get_records();

        $this->assertEquals($uclist[0]->get_userid(), $usercompetenciesplan[0]->get_userid());
        $this->assertEquals($uclist[0]->get_competencyid(), $usercompetenciesplan[0]->get_competencyid());
        $this->assertEquals($uclist[0]->get_proficiency(), (bool) $usercompetenciesplan[0]->get_proficiency());
        $this->assertEquals($uclist[0]->get_grade(), $usercompetenciesplan[0]->get_grade());
        $this->assertEquals($plan->get_id(), $usercompetenciesplan[0]->get_planid());

        $this->assertEquals($uclist[1]->get_userid(), $usercompetenciesplan[1]->get_userid());
        $this->assertEquals($uclist[1]->get_competencyid(), $usercompetenciesplan[1]->get_competencyid());
        $this->assertEquals($uclist[1]->get_proficiency(), (bool) $usercompetenciesplan[1]->get_proficiency());
        $this->assertEquals($uclist[1]->get_grade(), $usercompetenciesplan[1]->get_grade());
        $this->assertEquals($plan->get_id(), $usercompetenciesplan[1]->get_planid());

        $this->assertEquals($user->id, $usercompetenciesplan[2]->get_userid());
        $this->assertEquals($c3->get_id(), $usercompetenciesplan[2]->get_competencyid());
        $this->assertNull($usercompetenciesplan[2]->get_proficiency());
        $this->assertNull($usercompetenciesplan[2]->get_grade());
        $this->assertEquals($plan->get_id(), $usercompetenciesplan[2]->get_planid());

        // Completing a plan that is completed throws an exception.
        $this->setExpectedException('coding_exception');
        api::complete_plan($plan);
    }

    /**
     * Test update plan and the managing of archived user competencies.
     */
    public function test_update_plan_manage_archived_competencies() {
        global $DB;

        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');

        $syscontext = context_system::instance();

        // Create users and roles for the test.
        $user = $dg->create_user();
        $manageownrole = $dg->create_role(array(
            'name' => 'User manage own',
            'shortname' => 'manageown'
        ));
        assign_capability('tool/lp:planmanageowndraft', CAP_ALLOW, $manageownrole, $syscontext->id);
        assign_capability('tool/lp:planviewowndraft', CAP_ALLOW, $manageownrole, $syscontext->id);
        assign_capability('tool/lp:planmanageown', CAP_ALLOW, $manageownrole, $syscontext->id);
        assign_capability('tool/lp:planviewown', CAP_ALLOW, $manageownrole, $syscontext->id);
        $dg->role_assign($manageownrole, $user->id, $syscontext->id);
        $this->setUser($user);

        // Create a framework and assign competencies.
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));

        // Create two plans and assign competencies.
        $plan = $lpg->create_plan(array('userid' => $user->id));
        $otherplan = $lpg->create_plan(array('userid' => $user->id));

        $lpg->create_plan_competency(array('planid' => $plan->get_id(), 'competencyid' => $c1->get_id()));
        $lpg->create_plan_competency(array('planid' => $plan->get_id(), 'competencyid' => $c2->get_id()));
        $lpg->create_plan_competency(array('planid' => $plan->get_id(), 'competencyid' => $c3->get_id()));
        $lpg->create_plan_competency(array('planid' => $otherplan->get_id(), 'competencyid' => $c1->get_id()));

        $uclist = array(
            $lpg->create_user_competency(array(
                                            'userid' => $user->id,
                                            'competencyid' => $c1->get_id(),
                                            'proficiency' => true,
                                            'grade' => 1
                                        )),
            $lpg->create_user_competency(array(
                                            'userid' => $user->id,
                                            'competencyid' => $c2->get_id(),
                                            'proficiency' => false,
                                            'grade' => 2
                                        ))
        );

        // Change status of the plan to complete.
        $record = $plan->to_record();
        $record->status = \tool_lp\plan::STATUS_COMPLETE;

        try {
            $plan = api::update_plan($record);
            $this->fail('We cannot complete a plan using api::update_plan().');
        } catch (coding_exception $e) {
        }
        api::complete_plan($plan);

        // Check that user compretencies are now in user_competency_plan objects and still in user_competency.
        $this->assertEquals(2, \tool_lp\user_competency::count_records());
        $this->assertEquals(3, \tool_lp\user_competency_plan::count_records());

        $usercompetenciesplan = \tool_lp\user_competency_plan::get_records();

        $this->assertEquals($uclist[0]->get_userid(), $usercompetenciesplan[0]->get_userid());
        $this->assertEquals($uclist[0]->get_competencyid(), $usercompetenciesplan[0]->get_competencyid());
        $this->assertEquals($uclist[0]->get_proficiency(), (bool) $usercompetenciesplan[0]->get_proficiency());
        $this->assertEquals($uclist[0]->get_grade(), $usercompetenciesplan[0]->get_grade());
        $this->assertEquals($plan->get_id(), $usercompetenciesplan[0]->get_planid());

        $this->assertEquals($uclist[1]->get_userid(), $usercompetenciesplan[1]->get_userid());
        $this->assertEquals($uclist[1]->get_competencyid(), $usercompetenciesplan[1]->get_competencyid());
        $this->assertEquals($uclist[1]->get_proficiency(), (bool) $usercompetenciesplan[1]->get_proficiency());
        $this->assertEquals($uclist[1]->get_grade(), $usercompetenciesplan[1]->get_grade());
        $this->assertEquals($plan->get_id(), $usercompetenciesplan[1]->get_planid());

        $this->assertEquals($user->id, $usercompetenciesplan[2]->get_userid());
        $this->assertEquals($c3->get_id(), $usercompetenciesplan[2]->get_competencyid());
        $this->assertNull($usercompetenciesplan[2]->get_proficiency());
        $this->assertNull($usercompetenciesplan[2]->get_grade());
        $this->assertEquals($plan->get_id(), $usercompetenciesplan[2]->get_planid());

        // Change status of the plan to active.
        $record = $plan->to_record();
        $record->status = \tool_lp\plan::STATUS_ACTIVE;

        try {
            api::update_plan($record);
            $this->fail('Completed plan can not be edited');
        } catch (coding_exception $e) {
        }

        api::reopen_plan($record->id);
        // Check that user_competency_plan objects are deleted if the plan status is changed to another status.
        $this->assertEquals(2, \tool_lp\user_competency::count_records());
        $this->assertEquals(0, \tool_lp\user_competency_plan::count_records());
    }

    /**
     * Test remove plan and the managing of archived user competencies.
     */
    public function test_delete_plan_manage_archived_competencies() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');

        $syscontext = context_system::instance();

        // Create user and role for the test.
        $user = $dg->create_user();
        $managerole = $dg->create_role(array(
            'name' => 'User manage own',
            'shortname' => 'manageown'
        ));
        assign_capability('tool/lp:planmanageowndraft', CAP_ALLOW, $managerole, $syscontext->id);
        assign_capability('tool/lp:planmanageown', CAP_ALLOW, $managerole, $syscontext->id);
        $dg->role_assign($managerole, $user->id, $syscontext->id);
        $this->setUser($user);

        // Create a framework and assign competencies.
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));

        // Create completed plan with records in user_competency.
        $completedplan = $lpg->create_plan(array('userid' => $user->id, 'status' => \tool_lp\plan::STATUS_COMPLETE));

        $lpg->create_plan_competency(array('planid' => $completedplan->get_id(), 'competencyid' => $c1->get_id()));
        $lpg->create_plan_competency(array('planid' => $completedplan->get_id(), 'competencyid' => $c2->get_id()));

        $uc1 = $lpg->create_user_competency(array('userid' => $user->id, 'competencyid' => $c1->get_id()));
        $uc2 = $lpg->create_user_competency(array('userid' => $user->id, 'competencyid' => $c2->get_id()));

        $ucp1 = $lpg->create_user_competency_plan(array('userid' => $user->id, 'competencyid' => $c1->get_id(),
                'planid' => $completedplan->get_id()));
        $ucp2 = $lpg->create_user_competency_plan(array('userid' => $user->id, 'competencyid' => $c2->get_id(),
                'planid' => $completedplan->get_id()));

        api::delete_plan($completedplan->get_id());

        // Check that achived user competencies are deleted.
        $this->assertEquals(0, \tool_lp\plan::count_records());
        $this->assertEquals(2, \tool_lp\user_competency::count_records());
        $this->assertEquals(0, \tool_lp\user_competency_plan::count_records());
    }

    /**
     * Test listing of plan competencies.
     */
    public function test_list_plan_competencies_manage_archived_competencies() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');

        $syscontext = context_system::instance();

        // Create user and role for the test.
        $user = $dg->create_user();
        $viewrole = $dg->create_role(array(
            'name' => 'User view',
            'shortname' => 'view'
        ));
        assign_capability('tool/lp:planviewdraft', CAP_ALLOW, $viewrole, $syscontext->id);
        assign_capability('tool/lp:planview', CAP_ALLOW, $viewrole, $syscontext->id);
        $dg->role_assign($viewrole, $user->id, $syscontext->id);
        $this->setUser($user);

        // Create a framework and assign competencies.
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));

        // Create draft plan with records in user_competency.
        $draftplan = $lpg->create_plan(array('userid' => $user->id));

        $lpg->create_plan_competency(array('planid' => $draftplan->get_id(), 'competencyid' => $c1->get_id()));
        $lpg->create_plan_competency(array('planid' => $draftplan->get_id(), 'competencyid' => $c2->get_id()));
        $lpg->create_plan_competency(array('planid' => $draftplan->get_id(), 'competencyid' => $c3->get_id()));

        $uc1 = $lpg->create_user_competency(array('userid' => $user->id, 'competencyid' => $c1->get_id()));
        $uc2 = $lpg->create_user_competency(array('userid' => $user->id, 'competencyid' => $c2->get_id()));

        // Check that user_competency objects are returned when plan status is not complete.
        $plancompetencies = api::list_plan_competencies($draftplan);

        $this->assertCount(3, $plancompetencies);
        $this->assertInstanceOf('\tool_lp\user_competency', $plancompetencies[0]->usercompetency);
        $this->assertEquals($uc1->get_id(), $plancompetencies[0]->usercompetency->get_id());
        $this->assertNull($plancompetencies[0]->usercompetencyplan);

        $this->assertInstanceOf('\tool_lp\user_competency', $plancompetencies[1]->usercompetency);
        $this->assertEquals($uc2->get_id(), $plancompetencies[1]->usercompetency->get_id());
        $this->assertNull($plancompetencies[1]->usercompetencyplan);

        $this->assertInstanceOf('\tool_lp\user_competency', $plancompetencies[2]->usercompetency);
        $this->assertEquals(0, $plancompetencies[2]->usercompetency->get_id());
        $this->assertNull($plancompetencies[2]->usercompetencyplan);

        // Create completed plan with records in user_competency_plan.
        $completedplan = $lpg->create_plan(array('userid' => $user->id, 'status' => \tool_lp\plan::STATUS_COMPLETE));

        $pc1 = $lpg->create_plan_competency(array('planid' => $completedplan->get_id(), 'competencyid' => $c1->get_id()));
        $pc2 = $lpg->create_plan_competency(array('planid' => $completedplan->get_id(), 'competencyid' => $c2->get_id()));
        $pc3 = $lpg->create_plan_competency(array('planid' => $completedplan->get_id(), 'competencyid' => $c3->get_id()));

        $ucp1 = $lpg->create_user_competency_plan(array('userid' => $user->id, 'competencyid' => $c1->get_id(),
                'planid' => $completedplan->get_id()));
        $ucp2 = $lpg->create_user_competency_plan(array('userid' => $user->id, 'competencyid' => $c2->get_id(),
                'planid' => $completedplan->get_id()));

         // Check that an exception is thrown when a user competency plan is missing.
        try {
            $plancompetencies = api::list_plan_competencies($completedplan);
            $this->fail('All competencies in the plan must be associated to a user competency plan');
        } catch (coding_exception $e) {
            $this->assertTrue(true);
        }

        $ucp3 = $lpg->create_user_competency_plan(array('userid' => $user->id, 'competencyid' => $c3->get_id(),
                'planid' => $completedplan->get_id()));

        // Check that user_competency_plan objects are returned when plan status is complete.
        $plancompetencies = api::list_plan_competencies($completedplan);

        $this->assertCount(3, $plancompetencies);
        $this->assertInstanceOf('\tool_lp\user_competency_plan', $plancompetencies[0]->usercompetencyplan);
        $this->assertEquals($ucp1->get_id(), $plancompetencies[0]->usercompetencyplan->get_id());
        $this->assertNull($plancompetencies[0]->usercompetency);
        $this->assertInstanceOf('\tool_lp\user_competency_plan', $plancompetencies[1]->usercompetencyplan);
        $this->assertEquals($ucp2->get_id(), $plancompetencies[1]->usercompetencyplan->get_id());
        $this->assertNull($plancompetencies[1]->usercompetency);
        $this->assertInstanceOf('\tool_lp\user_competency_plan', $plancompetencies[2]->usercompetencyplan);
        $this->assertEquals($ucp3->get_id(), $plancompetencies[2]->usercompetencyplan->get_id());
        $this->assertNull($plancompetencies[2]->usercompetency);
    }

    public function test_create_template_cohort() {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');

        $c1 = $dg->create_cohort();
        $c2 = $dg->create_cohort();
        $t1 = $lpg->create_template();
        $t2 = $lpg->create_template();

        $this->assertEquals(0, \tool_lp\template_cohort::count_records());

        // Create two relations with mixed parameters.
        $result = api::create_template_cohort($t1->get_id(), $c1->id);
        $result = api::create_template_cohort($t1, $c2);

        $this->assertEquals(2, \tool_lp\template_cohort::count_records());
        $this->assertInstanceOf('tool_lp\template_cohort', $result);
        $this->assertEquals($c2->id, $result->get_cohortid());
        $this->assertEquals($t1->get_id(), $result->get_templateid());
        $this->assertEquals(2, \tool_lp\template_cohort::count_records_select('templateid = :id', array('id' => $t1->get_id())));
        $this->assertEquals(0, \tool_lp\template_cohort::count_records_select('templateid = :id', array('id' => $t2->get_id())));
    }

    public function test_delete_template_cohort() {
        $this->resetAfterTest(true);
        $this->setAdminUser();

        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');

        $c1 = $dg->create_cohort();
        $c2 = $dg->create_cohort();
        $t1 = $lpg->create_template();
        $t2 = $lpg->create_template();
        $tc1 = $lpg->create_template_cohort(array('templateid' => $t1->get_id(), 'cohortid' => $c1->id));
        $tc1 = $lpg->create_template_cohort(array('templateid' => $t2->get_id(), 'cohortid' => $c2->id));

        $this->assertEquals(2, \tool_lp\template_cohort::count_records());
        $this->assertEquals(1, \tool_lp\template_cohort::count_records_select('templateid = :id', array('id' => $t1->get_id())));
        $this->assertEquals(1, \tool_lp\template_cohort::count_records_select('templateid = :id', array('id' => $t2->get_id())));

        // Delete existing.
        $result = api::delete_template_cohort($t1->get_id(), $c1->id);
        $this->assertTrue($result);
        $this->assertEquals(1, \tool_lp\template_cohort::count_records());
        $this->assertEquals(0, \tool_lp\template_cohort::count_records_select('templateid = :id', array('id' => $t1->get_id())));
        $this->assertEquals(1, \tool_lp\template_cohort::count_records_select('templateid = :id', array('id' => $t2->get_id())));

        // Delete non-existant.
        $result = api::delete_template_cohort($t1->get_id(), $c1->id);
        $this->assertTrue($result);
        $this->assertEquals(1, \tool_lp\template_cohort::count_records());
        $this->assertEquals(0, \tool_lp\template_cohort::count_records_select('templateid = :id', array('id' => $t1->get_id())));
        $this->assertEquals(1, \tool_lp\template_cohort::count_records_select('templateid = :id', array('id' => $t2->get_id())));
    }

    /**
     * Test add evidence for existing user_competency.
     */
    public function test_add_evidence_existing_user_competency() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');

        $syscontext = context_system::instance();

        // Create users.
        $user = $dg->create_user();
        $this->setUser($user);

        // Create a framework and assign competencies.
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c3 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $uc = $lpg->create_user_competency(array('userid' => $user->id, 'competencyid' => $c1->get_id()));

        // Create an evidence and check it was created with the right usercomptencyid and information.
        $evidence = api::add_evidence($user->id, $c1->get_id(), 'invalidevidencedesc', 'tool_lp', '{"a": "b"}', 'url', 1);
        $this->assertEquals(1, \tool_lp\evidence::count_records());

        $evidence->read();
        $this->assertEquals($uc->get_id(), $evidence->get_usercompetencyid());
        $this->assertEquals('invalidevidencedesc', $evidence->get_descidentifier());
        $this->assertEquals('tool_lp', $evidence->get_desccomponent());
        $this->assertEquals((object) array('a' => 'b'), $evidence->get_desca());
        $this->assertEquals('url', $evidence->get_url());
        $this->assertEquals(1, $evidence->get_grade());
    }

    /**
     * Test add evidence for none existing user_competency.
     */
    public function test_add_evidence_no_existing_user_competency() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $this->getDataGenerator()->get_plugin_generator('tool_lp');

        $syscontext = context_system::instance();

        // Create users.
        $user = $dg->create_user();
        $this->setUser($user);

        // Create a framework and assign competencies.
        $framework = $lpg->create_framework();
        $c1 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));
        $c2 = $lpg->create_competency(array('competencyframeworkid' => $framework->get_id()));

        // Create an evidence.
        $evidence = api::add_evidence($user->id, $c1->get_id(), 'invalidevidencedesc', 'tool_lp');
        $this->assertEquals(1, \tool_lp\evidence::count_records());

        // Check a user_comptency was created with the same usercomptencyid.
        $this->assertEquals(1, \tool_lp\user_competency::count_records_select('id = :id',
                                                                              array('id' => $evidence->get_usercompetencyid())));
    }

    /**
     * Test update ruleoutcome for course_competency.
     */
    public function test_set_ruleoutcome_course_competency() {
        $this->resetAfterTest(true);
        $dg = $this->getDataGenerator();
        $lpg = $dg->get_plugin_generator('tool_lp');
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $course = $dg->create_course();

        $this->setAdminUser();
        $f = $lpg->create_framework();
        $c = $lpg->create_competency(array('competencyframeworkid' => $f->get_id()));
        $cc = api::add_competency_to_course($course->id, $c->get_id());

        // Check record was created with default rule value Evidence.
        $this->assertEquals(1, \tool_lp\course_competency::count_records());
        $recordscc = api::list_course_competencies($course->id);
        $this->assertEquals(\tool_lp\course_competency::OUTCOME_EVIDENCE, $recordscc[0]['coursecompetency']->get_ruleoutcome());

        // Check ruleoutcome value is updated to None.
        $this->assertTrue(api::set_course_competency_ruleoutcome($recordscc[0]['coursecompetency']->get_id(),
            \tool_lp\course_competency::OUTCOME_NONE));
        $recordscc = api::list_course_competencies($course->id);
        $this->assertEquals(\tool_lp\course_competency::OUTCOME_NONE, $recordscc[0]['coursecompetency']->get_ruleoutcome());
    }

}
