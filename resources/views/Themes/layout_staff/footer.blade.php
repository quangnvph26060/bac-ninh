<div class="row mt-4  custom-bclient-shadow custom-list-item">
    <div class="col-lg-6">
        <ul class="d-flex justify-content-between">
            <li class="d-flex justify-content-between align-items-center " id="fast-selling">
                {{-- <i class="fas fa-bolt"></i> Bán nhanh --}}
            </li>
            <li class="d-flex justify-content-between align-items-center" id="regular-selling">
                {{-- <i class="fas fa-shopping-bag"></i> Bán thường --}}
            </li>
            <li class="d-flex justify-content-between align-items-center" id="delivery-selling">
                {{-- <i class="fas fa-truck"></i> Bán giao hàng --}}
            </li>

        </ul>
    </div>
    <div class="col-lg-6">
        <li class="d-flex justify-content-end align-items-center" id="phone-number">
            {{-- <i class="fas fa-phone"></i> Số điện thoại: 1234567890 --}}
        </li>
    </div>
</div>
</div>
<!-- Right side: Footer -->
<!-- Modal -->
<div class="modal fade" id="customerModal" tabindex="-1" role="dialog" aria-labelledby="customerModalLabel"
aria-hidden="true">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="customerModalLabel">Thông tin khách hàng</h5></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-lg-12">
                    <form id="client" method="POST" action="{{ route('staff.client.add') }}">
                        @csrf
                        <div class="form-group">
                            <label for="fullName">Tên</label>
                            <input type="text" class="form-control" id="nameclient"
                                placeholder="Enter name" name="name">
                                <div class="col-lg-9"><span class="invalid-feedback d-block"
                                    style="font-weight: 500" id="clientName_error"></span> </div>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="emailclient"
                                placeholder="Enter email" name="email">
                                <div class="col-lg-9"><span class="invalid-feedback d-block"
                                    style="font-weight: 500" id="clientEmail_error"></span> </div>
                        </div>
                        <div class="form-group">
                            <label for="email">Địa chỉ</label>
                            <input type="text" class="form-control" id="addressclient"
                                placeholder="Enter address" name="address">
                                <div class="col-lg-9"><span class="invalid-feedback d-block"
                                    style="font-weight: 500" id="clientAddress_error"></span> </div>
                        </div>
                        <div class="form-group">
                            <label for="phoneNumber">Số điện thoại</label>
                            <input type="tel" class="form-control" id="phoneclient"
                                placeholder="Enter phone number" name="phone">
                                <div class="col-lg-9"><span class="invalid-feedback d-block"
                                    style="font-weight: 500" id="clientPhone_error"></span> </div>
                        </div>

                        {{-- <div class="form-group">
                            <label for="clientgroup">Nhóm khách hàng</label></label>
                            <select class="form-control" id="clientgroup" name="clientgroup">
                                <option value="">----- Nhóm khách hàng ----- </option>
                                @foreach ($clientgroup as $item )
                                <option value="{{ $item->id }}"> {{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div> --}}

                        <button type="buttom" onclick="submitclient(event)"  class="btn btn-primary btn-block mt-4">Lưu</button>
                    </form>
                </div>
                {{-- <div class="col-lg-6 user-image">
                    <img src="https://via.placeholder.com/150" alt="User Image">
                    <!-- Replace the src value with the actual image URL -->
                </div> --}}
            </div>
        </div>
    </div>
</div>
</div>
<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- JavaScript code -->
<script src="{{asset('js/staff.js')}}"></script>
<script>
    var validateClient = {
        'name': {
            'element': document.getElementById('nameclient'),
            'error': document.getElementById('clientName_error'),
            'validations': [
                {
                    'func': function(value){
                        return checkRequired(value);
                    },
                    'message': generateErrorMessage('E032')
                },
            ]
        },
        'email': {
            'element': document.getElementById('emailclient'),
            'error': document.getElementById('clientEmail_error'),
            'validations': [
                {
                    'func': function(value){
                        return checkRequired(value);
                    },
                    'message': generateErrorMessage('E033')
                },
            ]
        },
        'phoneNumber': {
            'element': document.getElementById('phoneclient'),
            'error': document.getElementById('clientPhone_error'),
            'validations': [
                {
                    'func': function(value){
                        return checkRequired(value);
                    },
                    'message': generateErrorMessage('E034')
                },
            ]
        },
        'address': {
            'element': document.getElementById('addressclient'),
            'error': document.getElementById('clientAddress_error'),
            'validations': [
                {
                    'func': function(value){
                        return checkRequired(value);
                    },
                    'message': generateErrorMessage('E035')
                },
            ]
        }

    }
    function submitclient(event){
        event.preventDefault();
            if (validateAllFields(validateClient)){
                document.getElementById('client').submit();
            }
    }
</script>
</body>

</html>
