<?php
add_shortcode('memorial_calc', 'memorial_calc_shortcode');

function memorial_calc_shortcode() {
    $data = include get_template_directory() . '/data/memorial-data.php';

    ob_start();
    ?>
    <div id="memorial-calc" class="grid gtc-2 gg-12">
      <!-- <h3>Калькулятор памятника</h3> -->
      <div class="wrap-stela">
        <label>Стела:</label>
        <select id="stela">
          <option value="">Выберите</option>
          <?php foreach ($data['stelly'] as $name => $price): ?>
            <option value="<?= esc_attr($name) ?>" data-price="<?= esc_attr($price) ?>">
              <?= esc_html($name) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="wrap-tumba">
        <label>Тумба:</label>
        <select id="tumba" disabled>
          <option value="">Сначала выберите стеллу</option>
        </select>
      </div>

      <div class="wrap-plitka">
        <label>Плитка:</label>
        <select id="plitka">
          <option value="0">Без плитки</option>
          <?php foreach ($data['plitki'] as $name => $price): ?>
            <option value="<?= esc_attr($price) ?>">
              <?= esc_html($name) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="wrap-tile-area ">
        <label>Площадь плитки (м²):</label>
        <input type="number" id="tileArea" value="0" min="0" max="9999" step="0.1" disabled>
      </div>

      <div class="wrap-uslugi col-1-2">
        <fieldset>
          <legend>Доп. услуги</legend>
          <?php foreach ($data['uslugi'] as $name => $price): ?>
            <label>
              <input type="checkbox" class="usluga" value="<?= esc_attr($price) ?>">
              <?= esc_html($name) ?>
            </label><br>
          <?php endforeach; ?>
        </fieldset>
      </div>

      <div id="calcResult" class="col-1-2" style="margin-top:15px; font-weight:bold;">Итого: 0 ₽</div>
      <div id="price-product" class="price-product d-none"><?php global $product; echo $product->get_price(); ?></div>
    </div>

    <script>
      const dataMap = <?= json_encode($data['mapping'], JSON_UNESCAPED_UNICODE) ?>;
      const tumbyAll = <?= json_encode($data['tumby'], JSON_UNESCAPED_UNICODE) ?>;

      document.addEventListener('DOMContentLoaded', () => {
        const stelaSelect = document.getElementById('stela');
        const tumbaSelect = document.getElementById('tumba');
        const plitkaSelect = document.getElementById('plitka');
        const tileAreaInput = document.getElementById('tileArea');
        const resultEl = document.getElementById('calcResult');

        const calculate = () => {
          const stelaVal = stelaSelect.value;
          const tumbaPrice = parseFloat(tumbaSelect.value) || 0;
          const tilePrice = parseFloat(plitkaSelect.value) || 0;
          const area = parseFloat(tileAreaInput.value) || 0;

          const stelaPrice = stelaVal ? parseFloat(stelaSelect.selectedOptions[0].dataset.price) || 0 : 0;
          
          let sum = <?php echo $product->get_price() ?>;

          if (stelaPrice > 0) { sum += stelaPrice }
          if (tumbaPrice > 0) { sum += tumbaPrice }
          if (tilePrice > 0 && area > 0) { sum += tilePrice * area }
          // sum += 6000 * 2; // Цветник 
          // sum += 4000 * 2; // Основание 
          
          // Обновление текста и цены услуги "Установка стелы"
          const updateInstallationPrice = () => {
            const stelaVal = stelaSelect.value;
            if (!stelaVal) return;

            const height = parseInt(stelaVal.split('×')[0]) || 0;
            const newPrice = height <= 1000 ? 8000 : 10000;

            document.querySelectorAll('.usluga').forEach(input => {
              const label = input.closest('label');
              if (!label) return;

              // ищем текстовый узел после input
              const textNode = [...label.childNodes].find(n => n.nodeType === 3); if (!textNode) return;

              // обновляем только услугу "Установка стелы"
              if (textNode.textContent.trim().startsWith('Установка стелы')) {
                input.value = newPrice;
                textNode.textContent = ` Установка стелы — ${newPrice.toLocaleString('ru-RU')} ₽`;
              }
            });
          };

          document.querySelectorAll('.usluga:checked').forEach(el => { sum += parseFloat(el.value) || 0 });

          console.log(sum)
          resultEl.textContent = `Итого: ${sum.toLocaleString('ru-RU')} ₽`;
        };

        // Обновление тумбы при выборе стеллы
        stelaSelect.addEventListener('change', () => {
          const val = stelaSelect.value;
          tumbaSelect.innerHTML = '<option value="">Выберите тумбу</option>';
          tumbaSelect.disabled = true;

          if (val && dataMap[val]) {
            tumbaSelect.disabled = false;
            dataMap[val].forEach(size => {
              if (tumbyAll[size]) {
                const opt = document.createElement('option');
                opt.value = tumbyAll[size];
                opt.textContent = `${size}`;
                tumbaSelect.appendChild(opt);
              }
            });
          }
          
          calculate();
        });

        // Активация/деактивация поля площади при выборе плитки
        plitkaSelect.addEventListener('change', () => {
          const hasTile = parseFloat(plitkaSelect.value) > 0;
          tileAreaInput.disabled = !hasTile;
          if (!hasTile) tileAreaInput.value = 1;
          calculate();
        });

        // Слушаем изменения
        tumbaSelect.addEventListener('change', calculate);
        tileAreaInput.addEventListener('input', calculate);
        document.querySelectorAll('.usluga').forEach(cb => {
          cb.addEventListener('change', calculate);
        });
        
        calculate(); // Первый расчёт (если что-то выбрано по умолчанию)

        // Сборка выбранных услуг
        let combineOrder = () => { 
          let getSelectedText = (e) => e.options[e.selectedIndex].text;
          let txtOrderArr = [];
          if (stela.value != '') { txtOrderArr.push(`Стела: ${getSelectedText(stela)}`)};
          if (tumba.value != '') { txtOrderArr.push(`Тумба: ${getSelectedText(tumba)}`)};
          if (plitka.value != 0) { txtOrderArr.push(`Плитка: ${getSelectedText(plitka)} (${tileArea.value}м²)`)};

          let countChecked = 0;
          document.querySelectorAll('.usluga').forEach(el => {if (countChecked > 0) txtOrderArr.push('\nДоп. услуги:') });
          document.querySelectorAll('.usluga').forEach(el => {
            if (el.checked) { txtOrderArr.push(`- ${el.closest('label').innerText}`) };
          });
          txtOrderArr.push(calcResult.innerText);
          txtOrderArr.join('\n');
          document.querySelector('#forminator-module-284 #textarea-1 textarea').value = txtOrderArr.join('\n');
          console.log(document.querySelector('#forminator-module-284 #textarea-1 textarea').value);
        }
  
        orderTombstone.addEventListener('click', combineOrder);

      });

      

    </script>
    <?php
    return ob_get_clean();
}