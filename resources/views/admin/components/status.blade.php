<div class="card">
    <div class="card-header">
        <h4 class="card-title"><label for="" class="form-label  required">Trạng thái</label>
        </h4>
    </div>
    <div class="card-body">
        <select name="status" class="form-select form-control" id="status">
            <option value="1" @selected(1 == $status)>Xuất bản </option>
            <option value="2" @selected(2 == $status)>Chưa xuất bản </option>
        </select>
    </div>
</div>
