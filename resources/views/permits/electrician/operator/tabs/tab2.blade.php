{{-- Tab 2: Address Information --}}
<div class="row">
    <div class="col-md-12 mb-3">
        <label for="village" class="form-label">Village/Area/House</label>
        <textarea class="form-control @error('village') is-invalid @enderror" id="village" name="village"
            rows="2">{{ old('village', $application->village) }}</textarea>
        @error('village')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="post_office" class="form-label">Post Office</label>
        <input type="text" class="form-control @error('post_office') is-invalid @enderror" id="post_office"
            name="post_office" value="{{ old('post_office', $application->post_office) }}">
        @error('post_office')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="postcode" class="form-label">Post Code</label>
        <input type="number" class="form-control @error('postcode') is-invalid @enderror" id="postcode" name="postcode"
            value="{{ old('postcode', $application->postcode) }}">
        @error('postcode')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="upazilla" class="form-label">Upazilla/Thana</label>
        <input type="text" class="form-control @error('upazilla') is-invalid @enderror" id="upazilla" name="upazilla"
            value="{{ old('upazilla', $application->upazilla) }}">
        @error('upazilla')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="district" class="form-label">District</label>
        <input type="text" class="form-control @error('district') is-invalid @enderror" id="district" name="district"
            value="{{ old('district', $application->district) }}">
        @error('district')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="division" class="form-label">Division</label>
        <select class="form-control @error('division') is-invalid @enderror" id="division" name="division">
            <option value="">Select Division</option>
            <option value="Dhaka" {{ old('division', $application->division) === 'Dhaka' ? 'selected' : '' }}>Dhaka
            </option>
            <option value="Chittagong" {{ old('division', $application->division) === 'Chittagong' ? 'selected' : '' }}>
                Chittagong</option>
            <option value="Rajshahi" {{ old('division', $application->division) === 'Rajshahi' ? 'selected' : '' }}>
                Rajshahi</option>
            <option value="Khulna" {{ old('division', $application->division) === 'Khulna' ? 'selected' : '' }}>Khulna
            </option>
            <option value="Barisal" {{ old('division', $application->division) === 'Barisal' ? 'selected' : '' }}>Barisal
            </option>
            <option value="Sylhet" {{ old('division', $application->division) === 'Sylhet' ? 'selected' : '' }}>Sylhet
            </option>
            <option value="Rangpur" {{ old('division', $application->division) === 'Rangpur' ? 'selected' : '' }}>Rangpur
            </option>
            <option value="Mymensingh" {{ old('division', $application->division) === 'Mymensingh' ? 'selected' : '' }}>
                Mymensingh</option>
        </select>
        @error('division')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>