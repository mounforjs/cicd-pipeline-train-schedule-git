<div class="modal fade" id="mobileFilterModal" role="dialog">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header p-2"><h4 class="whitetext"><span class="glyphicon glyphicon-lock"></span>FILTERS</h4>
				<button type="button" class="close" data-dismiss="modal">×</button>
			</div>

			<div class="modal-body p-2 col-xs-3">
				<form name="mobilefilters">
					<?php $this->load->view('layouts/filters/desktopfilters'); ?>
				</form>
			</div>
		</div>
	</div>
</div>