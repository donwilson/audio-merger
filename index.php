<?php
	
?><!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
	<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">
	<link href="static/css/jquery-ui.css" rel="stylesheet">
	
	<title>MP3 Merge</title>
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="static/js/jquery-ui.min.js"></script>
	<script src="static/js/dropzone.js"></script>
	<script>
		Dropzone.autoDiscover = false;
	</script>
	
	<style>
	    html, body {
	      height: 100%;
	    }
	    #actions {
	      margin: 2em 0;
	    }


	    /* Mimic table appearance */
	    div.table {
	      display: table;
	    }
	    div.table .file-row {
	      display: table-row;
	    }
	    div.table .file-row > div {
	      display: table-cell;
	      vertical-align: top;
	      border-top: 1px solid #ddd;
	      padding: 8px;
	    }
	    div.table .file-row:nth-child(odd) {
	      background: #f9f9f9;
	    }



	    /* The total progress gets shown by event listeners */
	    #total-progress {
	      opacity: 0;
	      transition: opacity 0.3s linear;
	    }

	    /* Hide the progress bar when finished */
	    #previews .file-row.dz-success .progress {
	      opacity: 0;
	      transition: opacity 0.3s linear;
	    }

	    /* Hide the delete button initially */
	    #previews .file-row .delete {
	      display: none;
	    }

	    /* Hide the start and cancel buttons and show the delete button */

	    #previews .file-row.dz-success .start,
	    #previews .file-row.dz-success .cancel {
	      display: none;
	    }
	    #previews .file-row.dz-success .delete {
	      display: block;
	    }
	  </style>
</head>
<body>
	
	<div class="container" id="container">
		<div id="actions" class="row">
			<div class="col-sm-7">
				<!-- The fileinput-button span is used to style the file input field as button -->
				<span class="btn btn-success fileinput-button">
					<i class="glyphicon glyphicon-plus"></i>
					<span>Add files...</span>
				</span>
				<button type="submit" class="btn btn-primary start">
					<i class="glyphicon glyphicon-upload"></i>
					<span>Start upload</span>
				</button>
				<button type="reset" class="btn btn-warning cancel">
					<i class="glyphicon glyphicon-ban-circle"></i>
					<span>Cancel upload</span>
				</button>
			</div>
			<div class="col-sm-5 text-center">
				<a href="#" class="btn btn-primary start_merge disabled">
					<i class="glyphicon glyphicon-circle-arrow-down"></i>
					<span>Merge Audio</span>
				</a>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-7">
				<!-- The global file processing state -->
				<span class="fileupload-process">
					<div id="total-progress" class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
						<div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress></div>
					</div>
				</span>
			</div>
			<div class="col-sm-5 text-center">
				<div id="status_bar">waiting...</div>
			</div>
		</div>
		
		<hr />
		
		<div class="table table-striped files" id="previews">
			<div id="template" class="file-row">
				<!-- This is used as the file preview template -->
				<div class="handle">
					<i class="glyphicon glyphicon-move"></i>
				</div>
				<div>
					<span class="preview"><img data-dz-thumbnail /></span>
				</div>
				<div>
					<p class="name" data-dz-name></p>
					<strong class="error text-danger" data-dz-errormessage></strong>
					<p>Temp File: <strong class="tmp_name"></strong></p>
					<input type="hidden" class="delete_key" value="" />
				</div>
				<div>
					<p class="size" data-dz-size></p>
					<div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
						<div class="progress-bar progress-bar-success" style="width:0%;" data-dz-uploadprogress></div>
					</div>
				</div>
				<div>
					<button class="btn btn-primary start">
						<i class="glyphicon glyphicon-upload"></i>
						<span>Start</span>
					</button>
					<button data-dz-remove class="btn btn-warning cancel">
						<i class="glyphicon glyphicon-ban-circle"></i>
						<span>Cancel</span>
					</button>
					<button data-dz-remove class="btn btn-danger delete">
						<i class="glyphicon glyphicon-trash"></i>
						<span>Delete</span>
					</button>
				</div>
			</div>
		</div>
	</div>
	
	<script type="text/javascript">
		;jQuery(document).ready(function($) {
			// sortable list
			$("#previews").sortable({
				'helper': "handle",
				'axis': "y"
			});
			
			
			function updateMergeButtonStatus() {
				var ready_to_merge = true;
				
				var num_exist = 0;
				var num_not_ready = 0;
				
				$("#previews .file-row").each(function() {
					num_exist++;
					
					if(!$(this).hasClass("dz-success")) {
						num_not_ready++;
					}
				});
				
				if((num_exist < 2) || (num_not_ready > 0)) {
					ready_to_merge = false;
				}
				
				if(num_exist) {
					$("#status_bar").empty().html("<strong>"+ (num_exist - num_not_ready) +"</strong> out of <strong>"+ num_exist +"</strong> ready");
				} else {
					$("#status_bar").empty().html("waiting...");
				}
				
				$("#actions .start_merge").toggleClass("disabled", !ready_to_merge);
			}
			
			function refreshPreviewsSortable() {
				$("#previews").sortable("refresh");
			}
			
			
			
			$("#actions .start_merge").on('click', function(e) {
				e.preventDefault();
				
				if($(this).hasClass("disabled")) {
					return;
				}
				
				//var sorted = $("#previews").sortable("serialize", {
				//	'key': "sort[]",
				//	'attribute': "data-tmp-file"
				//});
				
				var arrayed = $("#previews").sortable("toArray", {
					'attribute': "data-tmp-file"
				});
				
				//console.log("sorted", sorted, "arrayed", arrayed);
				
				// disable buttons, hide elements
				$("#previews").slideUp();
				$("#actions .btn").addClass("disabled");
				
				$("#total-progress").css({
					'opacity': 1
				});
				
				var progress = 15;
				$("#total-progress .progress-bar").css({
					'width': progress + "%"
				});
				
				$("#status_bar").empty().html("<em>Encoding...</em>");
				
				
				// make call to merge...
				
			});
			
			// Get the template HTML and remove it from the document
			var previewNode = document.querySelector("#template");
			previewNode.id = "";
			
			var previewTemplate = previewNode.parentNode.innerHTML;
			previewNode.parentNode.removeChild(previewNode);
			
			var myDropzone = new Dropzone(document.body, { // Make the whole body a dropzone
				'url': "upload.php", // Set the url
				'thumbnailWidth': 80,
				'thumbnailHeight': 80,
				'parallelUploads': 20,
				'previewTemplate': previewTemplate,
				'autoQueue': false, // Make sure the files aren't queued until manually added
				'previewsContainer': "#previews", // Define the container to display the previews
				'clickable': ".fileinput-button" // Define the element that should be used as click trigger to select files.
			});
			
			myDropzone.on('addedfile', function(file) {
				// Hookup the start button
				file.previewElement.querySelector(".start").onclick = function() {
					myDropzone.enqueueFile(file);
				};
				
				updateMergeButtonStatus();
				refreshPreviewsSortable();
			});
			
			// Update the total progress bar
			myDropzone.on('totaluploadprogress', function(progress) {
				document.querySelector("#total-progress .progress-bar").style.width = progress + "%";
			});
			
			myDropzone.on('sending', function(file) {
				// Show the total progress bar when upload starts
				document.querySelector("#total-progress").style.opacity = "1";
				// And disable the start button
				file.previewElement.querySelector(".start").setAttribute("disabled", "disabled");
				
				updateMergeButtonStatus();
			});
			
			// Hide the total progress bar when nothing's uploading anymore
			myDropzone.on('queuecomplete', function(progress) {
				document.querySelector("#total-progress").style.opacity = "0";
				
				updateMergeButtonStatus();
				refreshPreviewsSortable();
			});
			
			// successful upload
			myDropzone.on('success', function(file, response) {
				if(response && response.status && ("error" === response.status)) {
					myDropzone.accept(file, function() {
						if(response.message) {
							return response.message;
						}
						
						return "Failed to upload";
					});
					
					return;
				}
				
				myDropzone.accept(file, function() {
					file.previewElement.setAttribute('data-tmp-file', response.cargo.file_tmp);
					file.previewElement.querySelector(".tmp_name").innerHTML = response.cargo.file_tmp;
					
					if(response.cargo && response.cargo.delete_key) {
						file.previewElement.querySelector(".delete_key").value = response.cargo.delete_key;
					}
				});
			});
			
			// remove upload
			myDropzone.on('removedfile', function(file) {
				if(file.status && ("success" === file.status)) {
					// make an attempt to remove this file from server
					var tmp_name = file.previewElement.querySelector(".tmp_name").innerHTML || "";
					var delete_key = file.previewElement.querySelector(".delete_key").value || "";
					
					if(tmp_name && delete_key) {
						$.ajax({
							'method': "POST",
							'url': "delete.php",
							'cache': false,
							'data': {
								'tmp_name': tmp_name,
								'delete_key': delete_key
							}
						});
					}
				}
				
				refreshPreviewsSortable();
				updateMergeButtonStatus();
			});
			
			// upload complete
			myDropzone.on('canceled', function(file) {
				updateMergeButtonStatus();
			});
			
			// upload complete
			myDropzone.on('complete', function(file) {
				updateMergeButtonStatus();
			});
			
			// Setup the buttons for all transfers
			// The "add files" button doesn't need to be setup because the config
			// `clickable` has already been specified.
			$("#actions .start").on('click', function(e) {
				e.preventDefault();
				
				myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED));
				
				updateMergeButtonStatus();
			});
			
			$("#actions .cancel").on('click', function(e) {
				e.preventDefault();
				
				myDropzone.removeAllFiles(true);
				
				updateMergeButtonStatus();
			});
		});
	</script>
	
</body>
</html>