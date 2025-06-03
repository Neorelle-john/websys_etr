@extends('layouts.app')

@section('title', 'Add Employee')

@push('styles')
    <style>
        /* Your page-specific styles here */

        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .logo-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 30px;
        }

        .logo-container img {
            width: 150px;
            margin-bottom: 15px;
        }

        .logo-container h2 {
            color: #0A28D8;
            font-size: 22px;
            text-align: center;
        }

        .logo-container p {
            color: #999;
            font-size: 14px;
            text-align: center;
        }

        form h2 {
            font-size: 24px;
            color: #0A28D8;
            margin-bottom: 25px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            margin-bottom: 6px;
            color: #333;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #0A28D8;
            border-radius: 8px;
            background: #fff;
            color: #333;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #FFDA27;
            outline: none;
            box-shadow: 0 0 0 3px rgba(255, 218, 39, 0.5);
        }

        .form-group input::placeholder {
            color: #aaa;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background-color: #0A28D8;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            color: #f2f2f2;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-btn:hover {
            background-color: #FFDA27;
            color: #0A28D8;
        }

        /* New styles for image upload */
        .image-upload-container {
            border: 2px dashed #0A28D8;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            background: #f8f9fa;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .image-upload-container:hover {
            border-color: #FFDA27;
            background: #fff;
        }

        .image-upload-container.dragover {
            border-color: #FFDA27;
            background: #fff;
            box-shadow: 0 0 10px rgba(255, 218, 39, 0.3);
        }

        .image-upload-container i {
            font-size: 48px;
            color: #0A28D8;
            margin-bottom: 10px;
        }

        .upload-text {
            color: #666;
            margin: 10px 0;
        }

        #pictureInput {
            display: none;
        }

        #preview {
            max-width: 100%;
            max-height: 300px;
            margin-top: 15px;
            border-radius: 8px;
            display: none;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .preview-container {
            position: relative;
            display: inline-block;
        }

        .remove-image {
            position: absolute;
            top: 20px;
            right: 5px;
            background: #ff4444;
            color: white;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            cursor: pointer;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .remove-image:hover {
            background: #cc0000;
        }
    </style>
@endpush

@section('content')
    <div class="main-content">
        <div class="container">

            <form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <h2>Add Employee Data</h2>

                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Enter full name" />
                    @error('name')
                        <span style="color:red">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="id_number">ID:</label>
                    <input type="text" name="id_number" value="{{ old('id_number') }}" placeholder="Enter ID number" />
                    @error('id_number')
                        <span style="color:red">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="college">Choose College:</label>
                    <select name="college">
                        <option value="">Select</option>
                        <option value="College of Computing" {{ old('college') == 'College of Computing' ? 'selected' : '' }}>
                            College of Computing</option>
                        <option value="College of Teacher Education" {{ old('college') == 'College of Teacher Education' ? 'selected' : '' }}>College of Teacher Education</option>
                        <option value="College of Engineering" {{ old('college') == 'College of Engineering' ? 'selected' : '' }}>College of Engineering</option>
                        <option value="College of Architecture" {{ old('college') == 'College of Architecture' ? 'selected' : '' }}>College of Architecture</option>
                    </select>
                    @error('college')
                        <span style="color:red">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="classification">Choose Class Role:</label>
                    <select name="classification">
                        <option value="">Select</option>
                        <option value="Instructional" {{ old('classification') == 'Instructional' ? 'selected' : '' }}>
                            Instructional</option>
                        <option value="Non-instructional" {{ old('classification') == 'Non-instructional' ? 'selected' : '' }}>Non-instructional</option>
                    </select>
                    @error('classification')
                        <span style="color:red">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="picture">Employee Photo:</label>
                    <div class="image-upload-container" id="dropZone">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p class="upload-text">Drag and drop an image here or click to select</p>
                        <input type="file" name="picture" id="pictureInput" accept="image/*" hidden>
                        <div class="preview-container" id="previewContainer" style="display: none;">
                            <img id="preview" alt="Image Preview" style="max-width: 100px; max-height: 100px;"/>
                            <button type="button" class="remove-image" id="removeImage">&times;</button>
                        </div>
                    </div>
                    @error('picture')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>


                <button type="submit" class="login-btn">Save</button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const dropZone = document.getElementById('dropZone');
        const pictureInput = document.getElementById('pictureInput');
        const preview = document.getElementById('preview');
        const removeButton = document.getElementById('removeImage');

        // Handle drag and drop events
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            dropZone.classList.add('dragover');
        }

        function unhighlight(e) {
            dropZone.classList.remove('dragover');
        }

        dropZone.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;

            if (files.length > 0) {
                pictureInput.files = files;
                previewImage({ target: pictureInput });
            }
        }

        // Click to upload
        dropZone.addEventListener('click', () => {
            pictureInput.click();
        });

        function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function() {
                    preview.src = reader.result;
                    preview.style.display = 'block';
                    removeButton.style.display = 'flex';
                }
                reader.readAsDataURL(file);
            }
        }

        function removeImage() {
            pictureInput.value = '';
            preview.src = '';
            preview.style.display = 'none';
            removeButton.style.display = 'none';
        }
    </script>
@endpush
