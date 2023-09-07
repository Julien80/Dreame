<?php
if (!isConnect('admin')) {
	throw new Exception('{{401 - Accès non autorisé}}');
}
$plugin = plugin::byId('dreame');
sendVarToJS('eqType', $plugin->getId());
$eqLogics = eqLogic::byType($plugin->getId());
?>

<div class="row row-overflow">
	<div class="col-xs-12 eqLogicThumbnailDisplay">
		<div class="row">
			<div class="col-sm-10">
				<legend><i class="fas fa-cog"></i> {{Gestion}}</legend>
				<div class="eqLogicThumbnailContainer">
					<div class="cursor eqLogicAction logoPrimary" data-action="detectDevicesDreame">
						<i class="fas fa-search"></i>
						<br>
						<span>{{Détection Automatique}}</span>
					</div>
					<div class="cursor eqLogicAction logoSecondary" data-action="gotoPluginConf">
						<i class="fas fa-wrench"></i>
						<br>
						<span>{{Configuration}}</span>
					</div>


				</div>
			</div>

			<?php
			// uniquement si on est en version 4.4 ou supérieur
			$jeedomVersion  = jeedom::version() ?? '0';
			$displayInfoValue = version_compare($jeedomVersion, '4.4.0', '>=');
			if ($displayInfoValue) {
			?>
				<div class="col-sm-2">
					<legend><i class=" fas fa-comments"></i> {{Community}}</legend>
					<div class="eqLogicThumbnailContainer">
						<div class="cursor eqLogicAction logoSecondary" data-action="createCommunityPost">
							<i class="fas fa-ambulance"></i>
							<br>
							<span style="color:var(--txt-color)">{{Créer un post Community}}</span>
						</div>
					</div>
				</div>
			<?php
			}
			?>
		</div>
		<legend><i class="fas fa-table"></i> {{Mes Robots Aspirateur}}</legend>
		<input class="form-control" placeholder="{{Rechercher}}" id="in_searchEqlogic" />
		<div id="div_results"></div>
		<div class="eqLogicThumbnailContainer">
			<?php
			/** @var dreame $eqLogic */
			foreach ($eqLogics as $eqLogic) {

				$opacity = ($eqLogic->getIsEnable()) ? '' : 'disableCard';
				echo '<div class="eqLogicDisplayCard cursor ' . $opacity . '" data-eqLogic_id="' . $eqLogic->getId() . '">';
				echo '<img src="' . $eqLogic->getEqIcon() . '" />';
				echo '<br>';
				echo '<span class="name">' . $eqLogic->getHumanName(true, true) . '</span>';
				echo '</div>';
			}
			?>
		</div>
	</div>



	<div class="col-xs-12 eqLogic" style="display: none;">
		<div class="input-group pull-right" style="display:inline-flex">
			<span class="input-group-btn">
				<a class="btn btn-default btn-sm eqLogicAction roundedLeft" data-action="configure"><i class="fa fa-cogs"></i> {{Configuration avancée}}</a><a class="btn btn-default btn-sm eqLogicAction" data-action="copy"><i class="fas fa-copy"></i> {{Dupliquer}}</a><a class="btn btn-sm btn-success eqLogicAction" data-action="save"><i class="fas fa-check-circle"></i> {{Sauvegarder}}</a><a class="btn btn-danger btn-sm eqLogicAction roundedRight" data-action="remove"><i class="fas fa-minus-circle"></i> {{Supprimer}}</a>
			</span>
		</div>
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation"><a href="#" class="eqLogicAction" aria-controls="home" role="tab" data-toggle="tab" data-action="returnToThumbnailDisplay"><i class="fa fa-arrow-circle-left"></i></a></li>
			<li role="presentation" class="active"><a href="#eqlogictab" aria-controls="home" role="tab" data-toggle="tab"><i class="fas fa-tachometer-alt"></i> {{Equipement}}</a></li>
			<li role="presentation"><a href="#commandtab" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-list-alt"></i> {{Commandes}}</a></li>
		</ul>
		<div class="tab-content" style="height:calc(100% - 50px);overflow:auto;overflow-x: hidden;">
			<div role="tabpanel" class="tab-pane active" id="eqlogictab">
				<br />
				<a class="btn btn-warning btn-sm pull-right syncCmd" style="margin-top:5px;"><i class="fa fa-sync"></i> {{Synchroniser les Commandes}}</a><br /><br />
				<form class="form-horizontal">
					<fieldset>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Nom}}</label>
							<div class="col-sm-3">
								<input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
								<input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom}}" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Objet parent}}</label>
							<div class="col-sm-3">
								<select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
									<option value="">{{Aucun}}</option>
									<?php
									$options = '';
									foreach ((jeeObject::buildTree(null, false)) as $object) {
										$options .= '<option value="' . $object->getId() . '">' . str_repeat('&nbsp;&nbsp;', $object->getConfiguration('parentNumber')) . $object->getName() . '</option>';
									}
									echo $options;
									?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Catégorie}}</label>
							<div class="col-sm-9">
								<?php
								foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
									echo '<label class="checkbox-inline">';
									echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
									echo '</label>';
								}
								?>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label"></label>
							<div class="col-sm-9">
								<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isEnable" />{{Activer}}</label>
								<label class="checkbox-inline"><input type="checkbox" class="eqLogicAttr" data-l1key="isVisible" />{{Visible}}</label>
							</div>
						</div>

						<div class="form-group">
							<div class="col-sm-12">&nbsp;</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Adresse IP}}</label>
							<div class="col-sm-3">
								<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="ip" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Token}}</label>
							<div class="col-sm-3">
								<input type="text" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="token" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{ID unique}}</label>
							<div class="col-sm-3">
								<span class="eqLogicAttr" data-l1key="configuration" data-l2key="did"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label">{{Model}}</label>
							<div class="col-sm-3">
								<span class="eqLogicAttr" data-l1key="configuration" data-l2key="model"></span>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-3 control-label help" data-help="Le type par défaut est récupéré de votre appareil.<br/>Ne le modifiez que si lors de la synchronisation des commandes vous obtenez une erreur.">{{Type}}</label>
							<div class="col-sm-3">
								<select id="sel_robot" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="modelType">
									<option value="genericmiot">{{Générique}}</option>
									<option value="viomivacuum" class="optRobot hidden">{{Viomi}}</option>
									<option value="dreamevacuum" class="optRobot hidden">{{Dream}}</option>
									<option value="roborockvacuum" class="optRobot hidden">{{Roborock}}</option>
								</select>
								<br class="typeChange hidden" />
								<div class=" alert alert-danger typeChange hidden text-center">
									Attention, si vous changez le type vous devez relancer la synchronisation des commandes et perdrez potentiellement toutes les commandes existantes !
								</div>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-3 control-label help" data-help="Défini le temps entre 2 rafraichissements des status">{{Rafraichissement}}</label>
							<div class="col-sm-3">
								<select id="sel_robot" class="eqLogicAttr form-control" data-l1key="configuration" data-l2key="refresh">
									<option value="1">{{Toutes les minutes}}</option>
									<option value="5">{{Toutes les 5 min}}</option>
									<option value="15">{{Toutes les 15 min}}</option>
								</select>
							</div>
						</div>

					</fieldset>
				</form>
			</div>
			<div role="tabpanel" class="tab-pane" id="commandtab">
				<a class="btn btn-success btn-sm pull-right addCmd" style="margin-top:5px;"><i class="fa fa-plus-circle"></i> {{Commandes}}</a><br /><br />
				<table id="table_cmd" class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th>{{Id}}</th>
							<th>{{Nom}}</th>
							<th>{{Type}}</th>
							<th>{{Sous-Type}}</th>
							<th>{{Action}}</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>

	</div>
</div>

<?php include_file('desktop', 'dreame', 'js', 'dreame'); ?>
<?php include_file('desktop', 'dreame', 'css', 'dreame'); ?>
<?php include_file('core', 'plugin.template', 'js'); ?>