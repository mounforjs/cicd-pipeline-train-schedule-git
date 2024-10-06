$(document).ready(function () {
    $('#prizeModal').on('show.bs.modal', function (e) {
        $('#confirmcarousel').flexslider({
            animation: "slide",
            controlNav: false,
            animationLoop: true,
            slideshow: false,
            itemWidth: 112,
            itemMargin: 14,
            asNavFor: '#confirmcarousel'
        });

        $('#confirmslider').flexslider({
            animation: "slide",
            controlNav: false,
            animationLoop: true,
            slideshow: false,
            sync: "#confirmslider"
        });
    });

    $('#changePrizeAddress').click(function (e){
        selectAddress($("#selectedAddress").val());

        $('#changePrizeAddress').addClass("d-none");
        $('#confirmPrizeAddress').addClass("d-none");

        $('#cancelChangePrizeAddress').removeClass("d-none");
        if (parseInt($("#selectedAddress").val()) != $("#selectedAddress").data("selected")) {
            $("#changeAddress").removeClass("d-none");
        } else {
            $('#prizeModal .modal-footer').addClass("d-none");
        }

        $("#update_address").removeClass("d-none");
    });

    $('#cancelChangePrizeAddress').click(function (){
        $("#address-form").trigger("reset");

        if (parseInt($("#selectedAddress").val()) != $("#selectedAddress").data("selected")) {
            $("#selectableAddresses div[class*='selected']").removeClass("selected");
        }

        $("#cancelChangePrizeAddress").addClass("d-none");
        $("#changeAddress").addClass("d-none");

        $("#update_address").addClass("d-none");

        $('#prizeModal .modal-footer').removeClass("d-none");
        $("#changePrizeAddress").removeClass("d-none");
        $("#confirmPrizeAddress").removeClass("d-none");
    });

    $('#changeAddress').click(function (){
        var id = $("#selectedAddress").data("selected")
        $("#selectedAddress").val(id);

        $.ajax({
            url: '/address/getAddress',
            type: 'GET',
            data: {id: id},
            beforeSend: function () {
                $('#prizeModal').find(".load").removeClass('d-none');
            },
            complete: function () {
                $('#prizeModal').find(".load").addClass('d-none');
            },
            success: function (response) {
                response = JSON.parse(response);
                address = response.address;

                setDestination(address);

                $("#update_address form").trigger("reset");

                $("#cancelChangePrizeAddress").addClass("d-none");
                $("#changeAddress").addClass("d-none");

                $("#update_address").addClass("d-none");

                $("#changePrizeAddress").removeClass("d-none");
                $("#confirmPrizeAddress").removeClass("d-none");
            },
            error: function(data) {
                showSweetAlert("We could not get that address at his moment. Try again later.", "Whoops!", "error");
            }
        });
    });

    $('#addAddress').click(function (e){
        $("#address-form").trigger("reset");

        $('#cancelChangePrizeAddress').prop("disabled", true);

        $('#changeAddressData').addClass("d-none");
        $('#changeAddress').addClass("d-none");
        $('#updateAddress').addClass("d-none");
        $('#confirmPrizeAddress').addClass("d-none");

        $('#prizeModal .modal-footer').removeClass("d-none");
        $('#cancelNewAddress').removeClass("d-none");
        $('#addNewAddress').removeClass("d-none");

        $("#newAddress").removeClass("d-none");
    });

    $('#cancelNewAddress').click(function (e){
        originalAddress = undefined;

        $('#cancelNewAddress').addClass("d-none");
        $('#addNewAddress').addClass("d-none");
        $('#updateAddress').addClass("d-none");

        $('#newAddress').addClass("d-none");

        $("#address-form").trigger("reset");

        $('#changeAddressData').removeClass("d-none");
        if (!$("#destinationAddress").is(":visible")) {
            $('#prizeModal .modal-footer').addClass("d-none");
        } else {
            if (parseInt($("#selectedAddress").val()) != $("#selectedAddress").data("selected")) {
                $("#changeAddress").removeClass("d-none");
            } else {
                $('#prizeModal .modal-footer').addClass("d-none");
            }
        }

        $('#cancelChangePrizeAddress').prop("disabled", false);
    });

    $(document).on('click', 'button[id*="selectAddress"]', function (e){
        var id =  $(e.target).data("id");

        selectAddress(id);
    });

    function selectAddress(id) {
        $(document).find('button[id*="selectAddress"]').each(function(key, value) {
            if ($(this).data("id") != id) {
                $(this).removeClass("d-none");
                $(document).find("#address"+ key).removeClass("selected");
            } else {
                $(this).addClass("d-none");

                $(document).find("#address"+ key).addClass("selected");

                data = {
                    address_1: $("#addressData"+ key).find("p[id*='address_1']").text(),
                    address_2: $("#addressData"+ key).find("p[id*='address_2']").text(),
                    city: $("#addressData"+ key).find("span[id*='address_city']").text(),
                    state: $("#addressData"+ key).find("span[id*='address_state']").text(),
                    zip: $("#addressData"+ key).find("span[id*='address_zip']").text(),
                };
            }
        });

        $("#selectedAddress").data("selected", id);
        if (parseInt($("#selectedAddress").val()) != $("#selectedAddress").data("selected")) {
            $('#prizeModal .modal-footer').removeClass("d-none")
            $("#changeAddress").removeClass("d-none");
        } else {
            $('#prizeModal .modal-footer').addClass("d-none")
            $("#changeAddress").addClass("d-none");
        }
    }

    $(document).on('click', 'button[id*="editAddress"]', function (e){
        $("#address-form").trigger("reset");
        
        $("#changeAddress").addClass("d-none");

        getAddressForEdit($(this).data("id"));
    });

    $('#addNewAddress').click(function (e){
        var address = getFormAddress();

        validateAddress(address);
    });

    $('#updateAddress').click(function (e){
        var dirty = isAddressDirty();
        if (dirty) {
            var address = getFormAddress();

            validateAddress(address, true);
        } else {
            showSweetAlert("No changes have been made", 'Whoops!', 'error');
        }
    });

    function validateAddress(address, update=false) {
        var name = (address.fullname) ? (address.fullname + "\n") : "";
        var add1 = (address.address_1) ? (address.address_1 + "\n") : "";
        var add2 = (address.address_2) ? (address.address_2 + "\n") : "";
        var city = (address.city) ? (address.city + ", ") : "";
        var state = (address.state) ? (address.state + "\n") : "";
        var zip = (address.zip) ? (address.zip) : "";

        var $validator = $('#address-form').valid();
        if (!$validator) {
            $validator.focusInvalid();
        } else {
            $.ajax({
                url: '/address/validateAddress',
                type: 'POST',
                data: address,
                beforeSend: function () {
                    $('#prizeModal').find(".load").removeClass('d-none');
                },
                complete: function () {
                    $('#prizeModal').find(".load").addClass('d-none');
                },
                success: function (data) {
                    data = JSON.parse(data);
        
                    if (data.status == "success") {
                        var title = "Is this the correct address?";
                        var text = name + add1 + add2 + city + state + zip;
                        showSweetConfirm(text, title, "warning", function(confirmed) {
                            if (!confirmed) {
                                return false;
                            } else {
                                data.Address.name = address.address_name;
                                data.Address.fullname = address.fullname;
                                setFormAddress(data.Address);
                                
                                if (update) {
                                    editAddress(getFormAddress());
                                } else {
                                    addAddress(getFormAddress());
                                }
                            }     
                        }); 
                    } else {
                        var title = "Unable to validate. Are you sure you want to add this address?";
                        var text = name + add1 + add2 + city + state + zip;
                        showSweetConfirm(text, title, "warning", function(confirmed) {
                            if (!confirmed) {
                                return false;
                            } else {
                                setFormAddress(address);
                                
                                if (update) {
                                    editAddress(getFormAddress());
                                } else {
                                    addAddress(getFormAddress());
                                }
                            }     
                        }); 
                    }
                }
            });
        }
    }

    function appendAddress(view, address) {
        $("#selectableAddresses").append(view)
        if (!$("#destinationAddress").is(":visible")) {
            $("#selectedAddress").data("selected", address.id);

            $("#cancelChangePrizeAddress").removeClass("d-none");
            $("#destinationAddress").removeClass("d-none");
            setDestination(address);
        }

        $('#newAddress').addClass("d-none");

        $('#cancelNewAddress').addClass("d-none");
        $('#addNewAddress').addClass("d-none");

        $("#address-form").trigger("reset");

        $('#changeAddressData').removeClass("d-none");

        if ($("#selectedAddress").val() != $("#selectedAddress").data("selected")) {
            $('#prizeModal .modal-footer').removeClass("d-none")
            $("#changeAddress").removeClass("d-none");
        } else {
            $('#prizeModal .modal-footer').addClass("d-none");
        }

        $('#cancelChangePrizeAddress').prop("disabled", false);
    }

    function updateAddress(address) {
        var editedAddress = $("#selectableAddresses").find("button[data-id*='" + address.id + "']").closest("div[id*='address']");
        var addressData = $(editedAddress).find(".addressData");

        $(editedAddress).find(".addressName").text(address.address_name);
        $(addressData).find("p[id*='address_fullname']").text(address.fullname);
        $(addressData).find("p[id*='address_1']").text(address.address_1);
        $(addressData).find("p[id*='address_2']").text(address.address_2);
        $(addressData).find("p[id*='address_city']").text(address.city);
        $(addressData).find("p[id*='address_state']").text(address.state);
        $(addressData).find("p[id*='address_zip']").text(address.zip);

        $('#newAddress').addClass("d-none");

        $('#cancelNewAddress').addClass("d-none");
        $('#updateAddress').addClass("d-none");

        $("#address-form").trigger("reset");

        $('#changeAddressData').removeClass("d-none");

        if ($("#selectedAddress").val() != $("#selectedAddress").data("selected")) {
            $("#changeAddress").removeClass("d-none");
        } else {
            setDestination(address);
            $('#prizeModal .modal-footer').addClass("d-none");
        }

        $('#cancelChangePrizeAddress').prop("disabled", false);
    }

    function addAddress(address) {
        address.claim = true;

        $.ajax({
            url: '/address/newAddress',
            type: 'POST',
            data: address,
            beforeSend: function () {
                $('#prizeModal').find(".load").removeClass('d-none');
            },
            complete: function () {
                $('#prizeModal').find(".load").addClass('d-none');
            },
            success: function (data) {
                data = JSON.parse(data);
                if (data.status == "success") {
                    appendAddress(data.message, data.address);

                    showSweetAlert('Your address has been added!', 'Success!', 'success');
                } else {
                    showSweetAlert('Your address could not be added at this time, please try again later!', 'Whoops!', 'error');
                }
            }
        });
    }

    function editAddress(address) {
        address.id = $("#updateAddress").data("id");

        $.ajax({
            url: '/address/editAddress',
            type: 'POST',
            data: address,
            beforeSend: function () {
                $('#prizeModal').find(".load").removeClass('d-none');
            },
            complete: function () {
                $('#prizeModal').find(".load").addClass('d-none');
            },
            success: function (data) {
                data = JSON.parse(data);
                if (data.status == "success") {
                    updateAddress(address);

                    showSweetAlert('Your address has been updated!', 'Success!', 'success');
                } else {
                    showSweetAlert('Your address could not be added at this time, please try again later!', 'Whoops!', 'error');
                }
            }
        });
    }

    function getFormAddress() {
        var address = {
            address_name: $("#address_name").val(),
            fullname: $("#address_fullname").val(),
            address_1: $("#address_1").val(),
            address_2: $("#address_2").val(),
            city: $("#address_city").val(),
            state: $("#address_state").val(),
            zip: $("#address_zip").val(),
        };

        return address;
    }

    function setFormAddress(address) {
        $("#address_fullname").val(address.fullname);

        if (address.Address2) {
            $("#address_name").val(address.name);
            $("#address_1").val(address.Address2);
            $("#address_2").val(address.Address1);
            $("#address_city").val(address.City);
            $("#address_state").val(address.State);
            $("#address_zip").val(address.Zip5);
        } else {
            $("#address_name").val(address.address_name);
            $("#address_1").val(address.address_1);
            $("#address_2").val(address.address_2);
            $("#address_city").val(address.city);
            $("#address_state").val(address.state);
            $("#address_zip").val(address.zip);
        }
    }

    function setDestination(data) {
        $("#destfullname").text(data.fullname);
        $("#destaddress_1").text(data.address_1);
        $("#destaddress_2").text(data.address_2);
        $("#destcity").text(data.city);
        $("#deststate").text(data.state);
        $("#destzip").text(data.zip);
    }

    var originalAddress = undefined;
    function getAddressForEdit(id) {
        $.ajax({
            url: '/address/getAddress',
            type: 'GET',
            data: {id: id},
            beforeSend: function () {
                $('#prizeModal').find(".load").removeClass('d-none');
            },
            complete: function () {
                $('#prizeModal').find(".load").addClass('d-none');
            },
            success: function (response) {
                response = JSON.parse(response);
                data = response.address;

                $("#address_name").val(data.name);
                $("#address_fullname").val(data.fullname);
                $("#address_1").val(data.address_1);
                $("#address_2").val(data.address_2);
                $("#address_city").val(data.city);
                $("#address_state").val(data.state);
                $("#address_zip").val(data.zip);

                originalAddress = new FormData($('#address-form')[0]);

                $('#cancelChangePrizeAddress').prop("disabled", true);

                $('#changeAddressData').addClass("d-none");
                $('#changeAddress').addClass("d-none");
                $('#addNewAddress').addClass("d-none");
                $('#confirmPrizeAddress').addClass("d-none");

                $('#prizeModal .modal-footer').removeClass("d-none");
                $('#cancelNewAddress').removeClass("d-none");
                $('#updateAddress').data("id", id);

                $("#newAddress").removeClass("d-none");
            },
            error: function(data) {
                showSweetAlert("We could not get that address at his moment. Try again later.", "Whoops!", "error");
            }
        });
    }

    function isAddressDirty() {
        var currentAddress = new FormData($('#address-form')[0]);

        for (let [key, value] of currentAddress.entries()) {
            if (originalAddress.get(key) !== value) {
                return true;
            }
        }

        return false;
    }

    $("#address-form").on("change", function(e) {
        if (originalAddress != undefined) {
            var dirty = isAddressDirty();
            if (dirty) {
                $("#updateAddress").removeClass("d-none");
            } else {
                $("#updateAddress").addClass("d-none");
            }
        }
    });

    $('#confirmPrizeAddress').click(function (e){
        var name = $("#destfullname").text() + "\n";
        var add1 = $("#destaddress_1").text() + "\n";
        var add2 = $("#destaddress_2").text() + "\n";
        var city = $("#destcity").text() + ", ";
        var state = $("#deststate").text() + "\n";
        var zip = $("#destzip").text();

        var title = "Is this the correct address?";
        var text = name + add1 + add2 + city + state + zip;
        showSweetConfirm(text, title, "warning", function(confirmed) {
            if (!confirmed) {
                e.preventDefault();
            } else {
                var $validator = $('#address-form').valid();
                if (!$validator) {
                    $validator.focusInvalid();
                    return false;
                }

                var details = {
                    address_id: $("#selectedAddress").val(),
                    game_id: $("#prizeModal").data("id"),
                    slug: $("#prizeModal").data("slug")
                };

                $.ajax({
                    url: '/address/confirmAddress',
                    type: 'POST',
                    data: details,
                    beforeSend: function () {
                        $('#confirmPrizeAddress').prop("disabled", true);
                        $("#prizeModal").find(".load").removeClass('d-none');
                    },
                    complete: function () {
                        $("#prizeModal").find(".load").addClass('d-none');
                    },
                    success: function (data) {
                        data = JSON.parse(data);

                        $("#prizeModal").modal("hide");

                        if (data.status == "success") {
                            showSweetAlert("You've successfully claimed your prize! Check your dashboard for updates!", 'Success!', 'success');
                            window.setTimeout(function () {
                                window.location = data.redirect;
                            }, 5000);
                        } else {
                            showSweetAlert('We were not able to confirm your address at this time.', 'Whoops!', 'error');
                        }
                    }
                });
            }     
        }); 
    });
});
