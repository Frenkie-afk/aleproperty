<?php

class CommonHelper {
	public const IMG_ALLOWED_EXT = ['jpg', 'jpeg', 'png', 'webp'];
	public const IMG_ALLOWED_TYPES = ['image/jpg', 'image/jpeg', 'image/png', 'image/webp'];
	public const IMG_MAX_SIZE = 10485760; // 10mb

	public static function validate_image( string $field ) :array
	{
		$errors = [ 'size' => true, 'ext' => true ];
		$max_size = self::IMG_MAX_SIZE;
		$allowed_ext = self::IMG_ALLOWED_EXT;

		if ( !empty( $_FILES[$field]['name'] ) ) {
			$file = $_FILES[$field];

			if ( $max_size < $file['size'] ) {
				$errors['size'] = false;
			}

			$filetype = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'] );
			$is_allowed_ext = in_array( $filetype['ext'], $allowed_ext );

			if ( !$is_allowed_ext ) {
				$errors['ext'] = false;
			}
		}

		return $errors;
	}
}
