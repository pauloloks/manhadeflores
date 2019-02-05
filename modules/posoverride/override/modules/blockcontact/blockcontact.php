<?php
if (!defined('_PS_VERSION_'))
	exit;
class BlockcontactOverride extends Blockcontact
{	
	public function hookDisplayTop($params)
	{
		global $smarty;
		$tpl = 'blockcontact';
		if (isset($params['blockcontact_tpl']) && $params['blockcontact_tpl'])
			$tpl = $params['blockcontact_tpl'];
		if (!$this->isCached($tpl.'.tpl', $this->getCacheId()))
			$smarty->assign(array(
				'telnumber' => Configuration::get('BLOCKCONTACT_TELNUMBER'),
				'email' => Configuration::get('BLOCKCONTACT_EMAIL')
			));
		return $this->display(__FILE__, $tpl.'.tpl', $this->getCacheId());
	}
}