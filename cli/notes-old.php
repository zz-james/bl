      //   //prevent scaling up
      //   if((!$this->scaling_up || $i > $this->scaling_up  )
      //     && $img_size_w > $main_size_w && $img_size_h > $main_size_h
      //   ){
      //     //set real max zoom
      //     $this->zoom_max = $i-1;
      //     continue;
      //   }

      //   //fit main image to current zoom lvl
      //   $lvl_image = $start ? clone $main_image : $lvl_image;
      //   $lvl_image = $this->imageFit($lvl_image, $img_size_w, $img_size_h);

      //   //store
      //   $this->imageSave($lvl_image, $lvl_file);

        //clear
        // if($start){
        //   $this->unloadImage($main_image);
        // }
        // $start = false;

        // $this->profiler('MapTiler: Created Image for zoom level: '.$i);