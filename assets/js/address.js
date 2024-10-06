$(document).ready(function () {
    $('#addAddress').click(function (e){
        $("#address-form").trigger("reset");

        $("#addressModal .modal-header h3").text("Add Address");
        $("#newAddress").removeClass("d-none");
        $("#updateAddress").addClass("d-none");

        $("#addressModal").modal("show");
    });

    $(document).on('click', 'button[id*="editAddress"]', function (e){
        $("#address-form").trigger("reset");
        
        $("#addressModal .modal-header h3").text("Edit Address");
        $("#newAddress").addClass("d-none");
        $("#updateAddress").removeClass("d-none");

        $("#addressModal").modal("show");

        populateAddressForm($(this).data("id"));
    });

    $(document).on('click', 'button[id*="makeDefault"]', function (e){
        var defaultAddress = $("#address0");
        var elem = $(this);
        var address = $(elem).closest("div[id*='address']");
        var id = $(elem).data("id");

        $.ajax({
            url: '/address/makeAddressDefault',
            method: 'POST',
            data: {id: id},
            beforeSend: function () {
                $('#divLoading').addClass('show');
            },
            complete: function () {
                $('#divLoading').removeClass('show');
            },
            success: function (data) {
                data = JSON.parse(data);

                if (data.status == "success") {
                    var name = $(address).find(".col-sm-auto");
                    $("#defaultAddress").prependTo(name);

                    var defContainer = $(defaultAddress).find(".addressContainer");
                    var addContainer = $(address).find(".addressContainer");
                    defContainer.appendTo(address);
                    addContainer.appendTo(defaultAddress);

                    $(address).find('button[id*="makeDefault"]').removeClass("d-none");
                    $(elem).addClass("d-none");


                    showSweetAlert('Your address has been updated!', 'Great');
                } else {
                    showSweetAlert('We were unable to make that address your default! Try again later!', 'Whoops!', 'error');
                }
            }
        });
    });

    $(document).on('click', 'button[id*="removeAddress"]', function (e){
        var elem = $(this);
        var name = $(elem).closest(".row").find(".addressName").contents().get(0).nodeValue;
        var id = $(this).data("id");

        var title = "Remove address?";
        var text = "Are you sure you want to remove: " + name + "?"
        showSweetConfirm(text, title, "warning", function(confirmed) {
            if (!confirmed) {
                e.preventDefault();
            } else {
                $.ajax({
                    url: '/address/removeAddress',
                    method: 'POST',
                    data: {id: id},
                    beforeSend: function () {
                    $("#divLoading").find(".load").show();
                    },
                    complete: function () {
                    $("#divLoading").find(".load").hide();
                    },
                    success: function (data) {
                        data = JSON.parse(data);

                        if (data.status == "success") {
                            if (data.default) {
                                var address = $("button[data-id='" + data.default + "']").closest(".row");

                                var name = $(address).find(".addressName");
                                $("#defaultAddress").appendTo(name);

                                $(address).find("button[id*=makeDefault]").addClass("d-none");
                            }

                            showSweetAlert("Your address has been removed!", 'Success!', 'success');
                        
                            $(elem).closest(".addressContainer").parent().remove();
                        } else {
                            showSweetAlert("We were unable to remove your address. Please contact support for further assistance.", 'Uh oh!', 'error');
                        }
                    }
                });
            }     
        }); 
    });

    $('#newAddress').click(function (e){
        var address = getAddress();

        validateAddress(address);
    });

    $('#updateAddress').click(function (e){
        var address = getAddress();

        validateAddress(address, true);
    });

    function populateAddressForm(id) {
        $.ajax({
            url: '/address/getAddress',
            type: 'GET',
            data: {id: id},
            beforeSend: function () {
                $('#addressModal').find(".load").removeClass('d-none');
            },
            complete: function () {
                $('#addressModal').find(".load").addClass('d-none');
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

                $("#updateAddress").data("id", id);
            },
            error: function(data) {
                showSweetAlert("We could not get that address at his moment. Try again later.", "Whoops!", "error");
            }
        });
    }

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
                    $('#addressModal').closest(".load").removeClass('d-none');
                },
                complete: function () {
                    $('#addressModal').closest(".load").addClass('d-none');
                },
                success: function (data) {
                    data = JSON.parse(data);
        
                    if (data.status == "success") {
                        data.Address.fullname = address.fullname;

                        var title = "Is this the correct address?";
                        var text = name + add1 + add2 + city + state + zip;
                        showSweetConfirm(text, title, "warning", function(confirmed) {
                            if (!confirmed) {
                                return false;
                            } else {
                                setAddressFormValues(data.Address);

                                if (update) {
                                    editAddress();
                                } else {
                                    addAddress();
                                }
                            }     
                        }); 
                    } else {
                        var title = "Unable to validate. Are you sure you want to use this address?";
                        var text = name + add1 + add2 + city + state + zip;
                        showSweetConfirm(text, title, "error", function(confirmed) {
                            if (!confirmed) {
                                return false;
                            } else {
                                setAddressFormValues(address);

                                if (update) {
                                    editAddress();
                                } else {
                                    addAddress();
                                }
                            }     
                        }); 
                    }
                }
            });
        }
    }

    function editAddress() {
        var address = getAddress();
        address.id = $("#updateAddress").data("id");

        $.ajax({
            url: '/address/editAddress',
            type: 'POST',
            data: address,
            beforeSend: function () {
                $('#addressModal').find(".load").removeClass('d-none');
            },
            complete: function () {
                $('#addressModal').find(".load").addClass('d-none');
            },
            success: function (data) {
                data = JSON.parse(data);
                if (data.status == "success") {
                    updateAddress(address);

                    showSweetAlert('Your address has been updated!', 'Success!', 'success');
                } else {
                    showSweetAlert('Your address could not be updated at this time, please try again!', 'Oops', 'error');
                }
            }
        });
    }

    function addAddress() {
        var address = getAddress();

        $.ajax({
            url: '/address/newAddress',
            type: 'POST',
            data: address,
            beforeSend: function () {
                $('#addressModal').find(".load").removeClass('d-none');
            },
            complete: function () {
                $('#addressModal').find(".load").addClass('d-none');
            },
            success: function (data) {
                data = JSON.parse(data);
                if (data.status == "success") {
                    $("#selectableAddresses").children().first().append(data.message);
                    $("#addressModal").modal("hide");
                    $("#address-form").trigger("reset");

                    showSweetAlert('Your address has been added!', 'Success!', 'success');
                } else {
                    showSweetAlert('Your address could not be added at this time, please try again!', 'Whoops!', 'error');
                }
            }
        });
    }

    function updateAddress(address) {
        var editedAddress = $("#selectableAddresses").find("button[data-id*='" + address.id + "']").closest("div[id*='address']");
        var addressData = $(editedAddress).find(".addressData");

        $(editedAddress).find(".addressName").contents().get(0).nodeValue = address.address_name;
        $(addressData).find("span[id*='address_fullname']").text(address.fullname);
        $(addressData).find("span[id*='address_1']").text(address.address_1);
        $(addressData).find("span[id*='address_2']").text(address.address_2);
        $(addressData).find("span[id*='address_city']").text(address.city);
        $(addressData).find("span[id*='address_state']").text(address.state);
        $(addressData).find("span[id*='address_zip']").text(address.zip);

        $("#addressModal").modal("hide");

        $('#updateAddress').addClass("d-none");

        $("#address-form").trigger("reset");
        $('#newAddress').removeClass("d-none");
    }

    function getAddress() {
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

    function setAddressFormValues(data) {
        $("#address_fullname").val(data.fullname);

        if (data.Address2) {
            $("#address_1").val(data.Address2);
            $("#address_2").val(data.Address1);
            $("#address_city").val(data.City);
            $("#address_state").val(data.State);
            $("#address_zip").val(data.Zip5);
        } else {
            $("#address_1").val(data.address_1);
            $("#address_2").val(data.address_2);
            $("#address_city").val(data.city);
            $("#address_state").val(data.state);
            $("#address_zip").val(data.zip);
        }
    }
});
