/**
 * Scan
 * This page scans cable barcodes to be processed
 */
 
function initializePathSelector(result, cableEnd){
	var separator = '';
	$(result).each(function(index, source){
		if(cableEnd != ''){
			if(index>0){
				separator = '<span>.&#8203;</span>';
			}
			$(cableEnd).append(separator+'<a class="newPathSelector" href="#" data-type="select" data-pk="1"></a>');
		}
		$('.newPathSelector').editable({
			showbuttons: false,
			mode: 'inline',
			source: source.children,
			url: 'backend/retrieve_path_connector.php',
			params: function(params){
				var pathContainer = $(this).parent().attr('id');
				params.cableEnd = pathContainer == 'localConnectorPathContainer' ? $(document).data('localAttrPrefix') : $(document).data('remoteAttrPrefix');
				params.connectorID = pathContainer == 'localConnectorPathContainer' ? $(document).data('localConnectorID') : $(document).data('remoteConnectorID');
				params.action = 'SELECT';
				return params;
			},
			success: function(response){
				var responseJSON = JSON.parse(response);
				if (responseJSON.active == 'inactive'){
					window.location.replace("https://otterm8.com/app/login.php");
				} else if ($(responseJSON.error).size() > 0){
					displayError(responseJSON.error);
				} else {
					var localConnectorCode39 = $(document).data('localConnectorCode39');
					//var localConnectorCode39 = $('#dataLocalConnectorCode39').val();
					if(responseJSON.result != 'FIN'){
						var selectedElement = $(this);
						var selectedElementParent = $(selectedElement).parent();
						var selectedElementIndex = $(selectedElement).index();
						
						//clear the 'connected to' field
						var separator = '<span>. </span>';
						if(responseJSON.result[0].selected == 'clear'){
							$(selectedElementParent).empty();
							separator = '';
							buildFullPath(localConnectorCode39);
						}
						$(selectedElementParent).children().eq(selectedElementIndex+1).nextAll().editable('destroy').remove();
						$(selectedElementParent).append(separator+'<a class="newPathSelector" href="#" data-type="select" data-pk="1"></a>');
						initializePathSelector(responseJSON.result, '');
					} else {
						buildFullPath(localConnectorCode39);
					}
				}
			}
		});
		if(source.selected != ''){
			$('.newPathSelector').editable('setValue', source.selected);
		}
		$('.newPathSelector').removeClass('newPathSelector');
	});
}

function enableFinalize(){
	var localConnectorType = $(document).data('localConnectorType');
	var cableMediaType = $(document).data('cableMediaType');
	var remoteConnectorType = $(document).data('remoteConnectorType');
	
	if(localConnectorType & cableMediaType & remoteConnectorType) {
		$('#buttonFinalize').off('click');
		$('#buttonFinalize').on('click', function(){
			var data = {
				id: $(document).data('cableID'),
				property: 'cableEditable'
				};
			data = JSON.stringify(data);
			$.post('backend/process_cable.php', {'data':data}, function(response){
				var responseJSON = JSON.parse(response);
				if ($(responseJSON.error).size() > 0){
					displayError(responseJSON.error);
				} else {
					destroyEditables();
					$('.requiredFlag').empty();
					$('#buttonFinalize').hide();
				}
			});
		}).prop('disabled', false).show();
	} else {
		$('#buttonFinalize').prop('disabled', true);
	}
	$('#buttonFinalize').show();
	$('.requiredFlag').html('*');
}

function destroyEditables(){
	var localConnector = $('#localConnectorType').html();
	var cableLength = $('#cableLength').html();
	var cableMediaType = $('#cableMediaType').html();
	var remoteConnector = $('#remoteConnectorType').html();
	
	$('#localConnectorType').editable('destroy');
	$('#cableLength').editable('destroy');
	$('#cableMediaType').editable('destroy');
	$('#remoteConnectorType').editable('destroy');
	
	$('#localConnectorTypeContainer').html(localConnector);
	$('#cableLengthContainer').html(cableLength);
	$('#cableMediaTypeContainer').html(cableMediaType);
	$('#remoteConnectorTypeContainer').html(remoteConnector);
}

function buildFullPath(localConnectorCode39){
	var data = {connectorCode39: localConnectorCode39};
	data = JSON.stringify(data);
	$.post('backend/retrieve_path_full.php', {'data':data}).done(function(response){
		var responseJSON = JSON.parse(response);
		if (responseJSON.active == 'inactive'){
			window.location.replace('/app/login.php');
		} else if ($(responseJSON.error).size() > 0){
			displayError(responseJSON.error);
		} else {
			$('#pathContainer').html(responseJSON.success);
			$('.cableArrow').on('click', function(){
				var data = {codeResult: {code: $(this).attr('data-Code39')}};
				scanCallback(data);
			});
		}
	});
}

function validateCode39(scanData){
	return scanData.match('[a-zA-Z0-9]+') ? true : false;
}

function toggleSwitch(switch_elem, on){
    if (on){ // turn it on
        if ($(switch_elem)[0].checked){ // it already is so do 
            // nothing
        }else{
            $(switch_elem).trigger('click').attr("checked", "checked"); // it was off, turn it on
        }
    }else{ // turn it off
        if ($(switch_elem)[0].checked){ // it's already on so 
            $(switch_elem).trigger('click').removeAttr("checked"); // turn it off
        }else{ // otherwise 
            // nothing, already off
        }
    }
}

function scanCallback(data){
	
	var code = data.codeResult.code;
	if(validateCode39(code)) {
		$('#scanModal').modal('hide');
		
		var data = {
			connectorCode39: code,
			};
		data = JSON.stringify(data);
		
		$.post('backend/retrieve_connector_data.php', {'data':data}, function(response){
			var responseJSON = JSON.parse(response);
			if (responseJSON.active == 'inactive'){
				window.location.replace("https://otterm8.com/app/login.php");
			} else if ($(responseJSON.error).size() > 0){
				displayError(responseJSON.error);
			} else if (responseJSON.success != ''){
				var success = responseJSON.success;
				var cable = success.cable;
				$(document).data('cableID', cable.id);
				$(document).data('localConnectorID', cable[success.localAttrPrefix+'_id']);
				$(document).data('localConnectorCode39', cable[success.localAttrPrefix+'_code39']);
				$(document).data('localAttrPrefix', success.localAttrPrefix);
				
				$('#localConnectorCode39').html(cable[success.localAttrPrefix+'_code39']);
				$('#localConnectorTypeContainer').html(success.connectorTypeInfo[cable[success.localAttrPrefix+'_connector']]);
				getConnectorPath(success.localAttrPrefix, cable[success.localAttrPrefix+'_id'], 'local');
				
				$('#cableLengthContainer').html(cable.length);
				$('#cableUnitOfLength').html(cable.unitOfLength);
				$('#cableMediaTypeContainer').html(success.cableMediaTypeInfo[cable.mediaType]);
				
				if(cable[success.remoteAttrPrefix+'_id'] != 0) {
					$('#remoteVerify').show();
					$('#remoteInitialize').hide();
					
					$(document).data('remoteConnectorID', cable[success.remoteAttrPrefix+'_id']);
					$(document).data('remoteConnectorCode39', cable[success.remoteAttrPrefix+'_code39']);
					$(document).data('remoteAttrPrefix', success.remoteAttrPrefix);
					
					$('#remoteConnectorCode39').html(cable[success.remoteAttrPrefix+'_code39']);
					$('#remoteConnectorTypeContainer').html(success.connectorTypeInfo[cable[success.remoteAttrPrefix+'_connector']]);
					getConnectorPath(success.remoteAttrPrefix, cable[success.remoteAttrPrefix+'_id'], 'remote');
				} else {
					$('#remoteVerify').hide();
					$('#remoteInitialize').show();
				}
				
				$(document).data('verified', 'unknown');
				$('#buttonVerify').prop('disabled', false);
				handleVerification();
				
				if(cable.editable == 1) {
					handleEditables(success);
					$(document).data('localConnectorType', cable[success.localAttrPrefix+'_connector'] > 0 ? true : false);
					$(document).data('remoteConnectorType', cable[success.remoteAttrPrefix+'_connector'] > 0 ? true : false);
					$(document).data('cableMediaType', cable['mediaType'] > 0 ? true : false);
					enableFinalize();
				}
				
				buildFullPath(cable[success.localAttrPrefix+'_code39']);
			} else {
				var errMsg = ['Something unexpected happened.'];
				displayError(errMsg);
			}
		});
	}
}

function verifyCallback(data){
	var code = data.codeResult.code;
	if(validateCode39(code)) {
		$('#scanModal').modal('hide');
		
		var data = {
			connectorCode39: $(document).data('localConnectorCode39'),
			verifyCode39: code
			};
		data = JSON.stringify(data);
		
		$.post('backend/retrieve_connector_data.php', {'data':data}, function(response){
			var responseJSON = JSON.parse(response);
			if (responseJSON.active == 'inactive'){
				window.location.replace("https://otterm8.com/app/login.php");
			} else if ($(responseJSON.error).size() > 0){
				displayError(responseJSON.error);
			} else if (responseJSON.success != ''){
				$(document).data('verified', responseJSON.success.verified);
				handleVerification();
			} else {
				var errMsg = ['Something unexpected happened.'];
				displayError(errMsg);
			}
		});
	}
}

function initializeCallback(data) {
	
	var code = data.codeResult.code;
	if(validateCode39(code)) {
		$('#scanModal').modal('hide');
		
		var data = {
			connectorCode39: $(document).data('localConnectorCode39'),
			initializeCode39: code
			};
		data = JSON.stringify(data);
		
		$.post('backend/retrieve_connector_data.php', {'data':data}, function(response){
			var responseJSON = JSON.parse(response);
			if (responseJSON.active == 'inactive'){
				window.location.replace('/app/login.php');
			} else if ($(responseJSON.error).size() > 0){
				displayError(responseJSON.error);
			} else if (responseJSON.success != ''){
				$('#remoteVerify').show();
				$('#remoteInitialize').hide();
				
				var success = responseJSON.success;
				var cable = success.cable;
				
				$(document).data('remoteConnectorID', cable[success.remoteAttrPrefix+'_id']);
				$(document).data('remoteConnectorCode39', cable[success.remoteAttrPrefix+'_code39']);
				$(document).data('remoteAttrPrefix', success.remoteAttrPrefix);
				
				$('#remoteConnectorCode39').html(cable[success.remoteAttrPrefix+'_code39']);
				$('#remoteConnectorTypeContainer').html(success.connectorTypeInfo[cable[success.remoteAttrPrefix+'_connector']]);
				getConnectorPath(success.remoteAttrPrefix, cable[success.remoteAttrPrefix+'_id'], 'remote');
				
				$(document).data('verified', 'unknown');
				$('#buttonVerify').prop('disabled', false);
				handleVerification();
				
				if(cable.editable == 1) {
					handleEditables(success);
					$(document).data('localConnectorType', cable[success.localAttrPrefix+'_connector'] > 0 ? true : false);
					$(document).data('remoteConnectorType', cable[success.remoteAttrPrefix+'_connector'] > 0 ? true : false);
					$(document).data('cableMediaType', cable['mediaType'] > 0 ? true : false);
					enableFinalize();
				}
				
				buildFullPath(cable[success.localAttrPrefix+'_code39']);
			} else {
				var errMsg = ['Something unexpected happened.'];
				displayError(errMsg);
			}
		});
	}
}

function handleVerification(){
	var remoteConnectorVerified = $(document).data('verified');
	// Clear identifying verify button classes
	$("#buttonVerify").removeClass (function (index, className) {
		return (className.match (/(^|\s)btn-\S+/g) || []).join(' ');
	});
	$("#buttonVerifyIcon").removeClass (function (index, className) {
		return (className.match (/(^|\s)fa-\S+/g) || []).join(' ');
	});
	
	// Add verify button classes depending on verified status
	if (remoteConnectorVerified == 'yes') {
		$('#buttonVerify').addClass('btn-success');
		$('#buttonVerifyIcon').addClass('fa-check');
	} else if (remoteConnectorVerified == 'no') {
		$('#buttonVerify').addClass('btn-danger');
		$('#buttonVerifyIcon').addClass('fa-times');
	} else {
		$('#buttonVerify').addClass('btn-info');
		$('#buttonVerifyIcon').addClass('fa-exclamation');
	}
}

function handleEditables(success){
	var cable = success.cable;
	
	var localConnectorTypeHTML = '<span><a href="#" class="connectorType" id="localConnectorType" data-type="select" data-pk="'+cable[success.localAttrPrefix+'_id']+'" data-property="connectorType" data-connectorTypeEnd="localConnectorType"></a></span>';
	$('#localConnectorTypeContainer').html(localConnectorTypeHTML);
	
	var cableLengthHTML ='<span><a href="#" id="cableLength" data-type="number" data-min="1" data-pk="'+cable.id+'" data-property="cableLength"></a></span>';
	$('#cableLengthContainer').html(cableLengthHTML);
	
	var cableMediaTypeHTML = '<span><a href="#" id="cableMediaType" data-type="select" data-pk="'+cable.id+'" data-property="cableMediaType"></a></span>';
	$('#cableMediaTypeContainer').html(cableMediaTypeHTML);
	
	var remoteConnectorTypeHTML = '<span><a href="#" class="connectorType" id="remoteConnectorType" data-type="select" data-pk="'+cable[success.remoteAttrPrefix+'_id']+'" data-property="connectorType" data-connectorTypeEnd="remoteConnectorType">-</a></span>';
	$('#remoteConnectorTypeContainer').html(remoteConnectorTypeHTML);
	
	//Make connector type selectable
	$('.connectorType').editable({
		showbuttons: false,
		mode: 'inline',
		source: success.connectorTypeInfo,
		url: 'backend/process_cable.php',
		params: function(params){
			var data = {
				property: $(this).attr('data-property'),
				id: params.pk,
				value: params.value
			};
			params.data = JSON.stringify(data);
			return params;
		},
		success: function(response){
			var connectorTypeEnd = $(this).attr('data-connectorTypeEnd');
			var responseJSON = JSON.parse(response);
			if ($(responseJSON.error).size() > 0){
				displayError(responseJSON.error);
				return 'error';
			} else {
				$(document).data(connectorTypeEnd, true);
				enableFinalize();
			}
		}
	});

	//Make length selectable
	$('#cableLength').editable({
		showbuttons: false,
		mode: 'inline',
		url: 'backend/process_cable.php',
		params: function(params){
			var data = {
				property: $(this).attr('data-property'),
				id: params.pk,
				value: params.value
			};
			params.data = JSON.stringify(data);
			return params;
		},
		success: function(response){
			var responseJSON = JSON.parse(response);
			if ($(responseJSON.error).size() > 0){
				displayError(responseJSON.error);
				return 'error';
			}
		}
	});
	// Media type is required before length can be set
	if(success.cable.mediaType == 0) {
		$('#cableLength').editable('option', 'disabled', true);
	}
	
	//Make cable media type selectable
	$('#cableMediaType').editable({
		showbuttons: false,
		mode: 'inline',
		source: success.cableMediaTypeInfo,
		url: 'backend/process_cable.php',
		params: function(params){
			var data = {
				property: $(this).attr('data-property'),
				id: params.pk,
				value: params.value
			};
			params.data = JSON.stringify(data);
			return params;
		},
		success: function(response){
			var responseJSON = JSON.parse(response);
			if ($(responseJSON.error).size() > 0){
				displayError(responseJSON.error);
				return 'error';
			} else {
				$('#cableUnitOfLength').html(responseJSON.success);
				$('#cableLength').editable('option', 'disabled', false);
				$(document).data('cableMediaType', true);
				enableFinalize();
			}
		}
	});
	
	//Set value of connector type selectable
	$('#localConnectorType').editable('setValue', cable[success.localAttrPrefix+'_connector']);
	if(cable[success.remoteAttrPrefix+'_id'] > 0) {
		$('#remoteConnectorType').editable('setValue', cable[success.remoteAttrPrefix+'_connector']);
	}
	$('#cableLength').editable('setValue', cable.length);
	$('#cableMediaType').editable('setValue', cable.mediaType);
}

function getConnectorPath(attrPrefix, connectorID, cableEnd){
	//Make Local connected to selectable
	$.post('backend/retrieve_path_connector.php', {
		value: '0-#-0-0-0',
		cableEnd: attrPrefix,
		connectorID: connectorID,
		action: 'GET'
	}, function(response){
		var responseJSON = JSON.parse(response);
		if (responseJSON.error != ''){
			alert(responseJSON.error);
		} else {
			var pathContainer = cableEnd == 'local' ? $('#localConnectorPathContainer') : $('#remoteConnectorPathContainer');
			$(pathContainer).empty();
			initializePathSelector(responseJSON.result, pathContainer);
		}
	});
}

function configureApp(){
	var App = {
        init: function() {
            var self = this;

            Quagga.init(this.state, function(err) {
                if (err) {
                    return self.handleError(err);
                }
                App.attachListeners();
                App.checkCapabilities();
                Quagga.start();
            });
        },
        handleError: function(err) {
            console.log(err);
        },
        checkCapabilities: function() {
            var track = Quagga.CameraAccess.getActiveTrack();
            var capabilities = {};
            if (typeof track.getCapabilities === 'function') {
                capabilities = track.getCapabilities();
            }
			if(capabilities.torch) {
				$('#flashContainer').show();
			}
        },
        attachListeners: function() {
            var self = this;

            $("#torchCheckbox").on("change", function(e) {
                e.preventDefault();
				var target = $(e.target);
				var state = 'settings.torch';
				var value = target.prop('checked');
                self.setState('settings.torch', value);
            });
        },
        _accessByPath: function(obj, path, val) {
            var parts = path.split('.'),
                depth = parts.length,
                setter = (typeof val !== "undefined") ? true : false;

            return parts.reduce(function(o, key, i) {
                if (setter && (i + 1) === depth) {
                    if (typeof o[key] === "object" && typeof val === "object") {
                        Object.assign(o[key], val);
                    } else {
                        o[key] = val;
                    }
                }
                return key in o ? o[key] : {};
            }, obj);
        },
        _convertNameToState: function(name) {
            return name.replace("_", ".").split("-").reduce(function(result, value) {
                return result + value.charAt(0).toUpperCase() + value.substring(1);
            });
        },
        detachListeners: function() {
			$("#torchCheckbox").off("change");
        },
        applySetting: function(setting, value) {
            var track = Quagga.CameraAccess.getActiveTrack();
            if (track && typeof track.getCapabilities === 'function') {
                switch (setting) {
                case 'zoom':
                    return track.applyConstraints({advanced: [{zoom: parseFloat(value)}]});
                case 'torch':
                    return track.applyConstraints({advanced: [{torch: !!value}]});
                }
            }
        },
        setState: function(path, value) {
            var self = this;

            if (typeof self._accessByPath(self.inputMapper, path) === "function") {
                value = self._accessByPath(self.inputMapper, path)(value);
            }

            if (path.startsWith('settings.')) {
                var setting = path.substring(9);
                return self.applySetting(setting, value);
            }
            self._accessByPath(self.state, path, value);

            console.log(JSON.stringify(self.state));
            App.detachListeners();
            Quagga.stop();
            App.init();
        },
        inputMapper: {
            inputStream: {
                constraints: function(value){
                    if (/^(\d+)x(\d+)$/.test(value)) {
                        var values = value.split('x');
                        return {
                            width: {min: parseInt(values[0])},
                            height: {min: parseInt(values[1])}
                        };
                    }
                    return {
                        deviceId: value
                    };
                }
            },
            numOfWorkers: function(value) {
                return parseInt(value);
            },
            decoder: {
                readers: function(value) {
                    if (value === 'ean_extended') {
                        return [{
                            format: "ean_reader",
                            config: {
                                supplements: [
                                    'ean_5_reader', 'ean_2_reader'
                                ]
                            }
                        }];
                    }
                    return [{
                        format: value + "_reader",
                        config: {}
                    }];
                }
            }
        },
        state: {
            inputStream: {
				name : "Live",
				type : "LiveStream",
				target: document.querySelector('#scanner'),
                constraints: {
                    width: {min: 200},
                    height: {min: 200},
                    facingMode: "environment"
                }
            },
            locator: {
                patchSize: "x-large",
                halfSample: true
            },
            numOfWorkers: navigator.hardwareConcurrency,
            frequency: 10,
            decoder : {
				readers : ["code_39_reader"]
			},
            locate: false,
			multiple: false
        },
        lastResult : null
    };
	
	return App;
}

$(document).ready(function() {

	$(document).data('verified', 'unknown');
	$(document).data('localConnectorType', false);
	$(document).data('remoteConnectorType', false);
	$(document).data('cableMediaType', false);
	var App = configureApp();
	
	$('#buttonScan').on('click', function(){
		$('#alertMsg').empty();
		$(document).data('scanFunction', scanCallback);
		$('#scanModal').modal('show');
	});
	
	$('#buttonVerify').on('click', function(){
		$('#alertMsg').empty();
		$(document).data('scanFunction', verifyCallback);
		$('#scanModal').modal('show');
	});
	
	$('#buttonInitialize').on('click', function(){
		$('#alertMsg').empty();
		$(document).data('scanFunction', initializeCallback);
		$('#scanModal').modal('show');
	});
	
	$('#scanModal').on('hidden.bs.modal', function (e) {
		toggleSwitch($('#torchCheckbox'), false);
		Quagga.stop();
		Quagga.offDetected();
		Quagga.initialized = undefined;
	});
	
	$('#scanModal').on('shown.bs.modal', function () {
		$('#manualCheckbox').on('change', function(e){
			var manualScan = $(e.target).prop('checked');
			if(manualScan) {
				$('#scannerContainer').hide();
				$('#manualEntry').show();
				toggleSwitch($('#torchCheckbox'), false);
				Quagga.stop();
				Quagga.offDetected();
				Quagga.initialized = undefined;
			} else {
				if($('#scanModal').hasClass('in')) {
					if(Quagga.initialized == null) {
						App.init();
						Quagga.initialized = true;
						Quagga.onDetected($(document).data('scanFunction'));
					}
				}
				$('#manualEntry').hide();
				$('#scannerContainer').show();
			}
		});
		
		if($('#scanModal').hasClass('in')) {
			if($('#scannerContainer').is(':visible')) {
				if(Quagga.initialized == null) {
					App.init();
					Quagga.initialized = true;
					Quagga.onDetected($(document).data('scanFunction'));
				}
			}
		}
	});
	
	$('#manualEntrySubmit').on('click', function(e){
		e.preventDefault();
		var code = $('#manualEntryInput').val();
		var data = {
			codeResult: {
				code: code
			}
		};
		$(document).data('scanFunction')(data);
	});
	
	if($('#connectorCodeParam').length) {
		var connectorCode = $('#connectorCodeParam').val();
		var data = {codeResult:{code:connectorCode}};
		scanCallback(data);
	}
});

