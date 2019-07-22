
<!--
/////////////////////////////
//Placeable objects
/////////////////////////////
-->
<?php
$templateCatalog = true;
include($_SERVER['DOCUMENT_ROOT'].'/app/includes/content-build-objectData.php');

foreach($templates as $category => $categoryTemplate) {
	echo '<div class="categoryCatalogContainerEntire">';
		echo '<h4 class="cursorPointer templateCatalogCategoryTitle" data-categoryName="'.$category.'"><i class="fa fa-caret-right"></i>'.$category.'</h4>';
		echo '<div class="templateCatalogCategory'.$category.'Container templateCatalogCategoryContainer" style="display:none;">';
		foreach ($categoryTemplate as $template) {
			$x=0;
			if (isset($template['partitionData'][$x])) {
				$partitionData = $template['partitionData'][$x];
				$ID = $template['id'];
				$type = $template['type'];
				
				echo '<div class="object-wrapper object'.$ID.'" data-templateName="'.$template['name'].'">';
				echo '<h4 class="templateName'.$ID.' header-title m-t-0 m-b-15">'.$template['name'].'</h4>';
				$RUSize = $template['RUSize'];
				$mountConfig = $template['mountConfig'];
				$function = $template['function'];
				$style = 'background-color: '.$template['categoryColor'].'; color: '.color_inverse($template['categoryColor']).';';
				if ($type == 'Standard'){
					//echo '<div class="obj'.$ID.' RU'.$RUSize.' category'.$category.' obj-style obj-border stockObj draggable selectable" data-templateid="'.$ID.'" data-objectFace="'.$x.'"  data-objectMountConfig="'.$mountConfig.'" data-RUSize="'.$RUSize.'" data-objectFunction="'.$function.'">';
					echo '<div class="obj'.$ID.' RU'.$RUSize.' obj-style obj-border stockObj draggable selectable" data-templateid="'.$ID.'" data-objectFace="'.$x.'"  style="'.$style.'" data-objectMountConfig="'.$mountConfig.'" data-RUSize="'.$RUSize.'" data-objectFunction="'.$function.'">';
					echo buildStandard($partitionData, 'flex-container-parent', $portType);
					echo '</div>';
				} else {
					$flexWidth = $partitionData[0]['hunits']/10;
					$flexHeight = 1/$template['encLayoutY'];
					$dataAttr = ' data-templateid="'.$ID.'" data-objectFace="'.$x.'"  data-objectMountConfig="'.$mountConfig.'" data-RUSize="'.$RUSize.'" data-objectFunction="'.$function.'"';
					$class = ' category'.$category.' stockObj obj-style obj-border insertDraggable selectable';
					echo '<div class="obj'.$ID.' RU'.$RUSize.'">';
					// Flex Container
					echo '<div class="flex-container-parent" style="flex-direction:row;">';
					// Define Width
					echo '<div class="flex-container" style="flex-direction:column;flex:'.$flexWidth.';">';
					// Define Height
					echo '<div class="flex-container" style="flex:'.$flexHeight.';">';
					echo '<table style="height:100%; width:100%;">';
					echo '<tr>';
					for($encX=0; $encX<$template['encLayoutX']; $encX++) {
						echo '<td style="flex-direction:column; width:'.round((1/$template['encLayoutX'])*100).'%; height:'.round((1/$template['encLayoutY'])*100).'%;">';
						if($encX == 0) {
							echo buildInsert($partitionData, $portType, $RUSize, $class, $dataAttr, $style);
						}
						echo '</td>';
					}
					echo '</tr>';
					echo '</table>';
					echo '</div>';
					echo '</div>';
					echo '</div>';
					echo '</div>';
				}
				echo '</div>';
			}
		}
		echo '</div>';
	echo '</div>';
}
?>