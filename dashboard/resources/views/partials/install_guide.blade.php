<div class="modal fade" id="guideline" data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
				<h5 class="modal-title">
					Installation
				</h5>
			</div>
			<div class="modal-body">
				<div class="bs-callout bs-callout-primary">
					<small class="uppercase"><b>Let metrics help you to understand your customers in one click!</b></small>
					<br>
					<small>
						Meotrics helps you to understand every single action from your customer, by tagging each action and property
						along with a specific person. By analyzing those specific detail, you get more accurate idea of who your
						customers are and what do they do in your site/app.
						Just one click and you can have all data that you need:
					</small>
				</div>
				<div class="step step-1">
					<div class="step-title text-primary">
						<div class="step-number">1</div>
						<h5 class="step-text">Step 1: Just a bit of code</h5>
					</div>
					<small>Paste this snippet just before the <b>&lt;/head&gt;</b> tag of your page:</small>
					<div class="well well-sm meotrics_script">
						<button class="btn btn-sm btn-primary btn-fill copy-btn" onclick="copyScript()">
							Copy
						</button>
						<textarea onkeydown="return false" placeholder="Loading..."></textarea>
					</div>
				</div>
				<div class="step step-2">
					<div class="step-title text-primary">
						<div class="step-number">2</div>
						<h5 class="step-text">Step 2: Refresh your website</h5>
					</div>
					<small>Just to let us test that we have well receive your request</small>
				</div>
				<div class="step step-3">
					<div class="step-title text-primary">
						<div class="step-number">3</div>
						<h5 class="step-text">Step 3: Complete! Lets start your journey</h5>
					</div>
					<small>Just give us a few second for checking, then refresh your browser and you can start using Meotrics. Or
						else, you still can explore by clicking on Demo icon on the left conner.
					</small>
				</div>
			</div>
			<div class="modal-footer">
				<small>Done ?</small>
				&nbsp;
				<button type="button" class="btn btn-sm btn-fill btn-primary"
				        data-dismiss="modal" aria-label="Close">
					Close
				</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
	function copyScript() {
		var el = $('#guideline').find('textarea');
		el.select();
		document.execCommand('selectAll');
		document.execCommand('copy');
		$.notify({
			icon: "pe-7s-copy-file",
			message: "Copied !"
		}, {
			type: 'success',
			timer: 2000,
			placement: {
				from: 'top',
				align: 'right'
			}
		});
		if (document.selection) {
			document.selection.empty();
		} else if (window.getSelection) {
			window.getSelection().removeAllRanges();
		}
	}
	function getIntegrationCode(appcode) {
		$.get('/mt.min.html', function (response) {
			var html = $('<div/>').text(response).html();
			$('#guideline').find('textarea').html(html.replace('$APPID$', appcode));
		}).fail(function (err) {
			_helper.notification.error(err.statusText)
		});
	}

	function showCodeDialog(appcode, callback) {
		if (callback !== undefined)
			$('#guideline').on('hidden.bs.modal', function () {
				callback();
			});
		$('#guideline').modal('show');
		getIntegrationCode(appcode);
	}

	$(document).ready(function () {
		$.get('/app/setup_status/{{$appcode}}', function (response) {
			console.log(response);
			if (response == "true" || response == 1) {
				//'Integrated !'
			} else {
				showCodeDialog(appcode);
			}
		}).fail(function (err) {
			_helper.notification.error('Failed to check installation !')
		});
	});
</script>