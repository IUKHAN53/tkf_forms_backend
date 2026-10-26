{{--
    Person fields for the attendance sheet / IIT member forms.
    Field names match Participant::adminRules(). $prefix keeps element ids unique
    when several forms share a page; JS fills values via [data-field="..."].
--}}
@php $prefix = $prefix ?? 'person'; @endphp
<div class="form-grid-2">
    <div class="form-group">
        <label class="form-label" for="{{ $prefix }}Name">Name *</label>
        <input type="text" id="{{ $prefix }}Name" name="name" data-field="name" class="form-input" maxlength="255" required>
    </div>
    <div class="form-group">
        <label class="form-label" for="{{ $prefix }}Gender">Gender</label>
        <select id="{{ $prefix }}Gender" name="gender" data-field="gender" class="form-input">
            <option value="">—</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label" for="{{ $prefix }}Role">Role / Designation</label>
        <input type="text" id="{{ $prefix }}Role" name="title_designation" data-field="title_designation" class="form-input" maxlength="255" placeholder="e.g. Religious leader, LHW, Teacher">
    </div>
    <div class="form-group">
        <label class="form-label" for="{{ $prefix }}Occupation">Occupation</label>
        <input type="text" id="{{ $prefix }}Occupation" name="occupation" data-field="occupation" class="form-input" maxlength="255">
    </div>
    <div class="form-group">
        <label class="form-label" for="{{ $prefix }}Contact">Contact No.</label>
        <input type="text" id="{{ $prefix }}Contact" name="contact_no" data-field="contact_no" class="form-input" maxlength="30" placeholder="03XX-XXXXXXX">
    </div>
    <div class="form-group">
        <label class="form-label" for="{{ $prefix }}Cnic">CNIC</label>
        <input type="text" id="{{ $prefix }}Cnic" name="cnic" data-field="cnic" class="form-input" maxlength="20" placeholder="XXXXX-XXXXXXX-X">
    </div>
</div>
<div class="form-group">
    <label class="form-label" for="{{ $prefix }}Address">Address</label>
    <input type="text" id="{{ $prefix }}Address" name="address" data-field="address" class="form-input" maxlength="500">
</div>
