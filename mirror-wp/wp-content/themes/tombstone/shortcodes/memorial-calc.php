<?php
add_shortcode('memorial_calc', 'memorial_calc_shortcode');

function memorial_calc_shortcode() {
    $data = include get_template_directory() . '/data/memorial-data.php';

    ob_start();
    ?>
    <div id="memorial-calc" class="grid gtc-2 gg-12">
      <!-- <h3>Калькулятор памятника</h3> -->
      <div class="wrap-stella">
        <label>Стелла:</label>
        <select id="stella">
          <option value="">Выберите</option>
          <?php foreach ($data['stelly'] as $name => $price): ?>
            <option value="<?= esc_attr($name) ?>" data-price="<?= esc_attr($price) ?>">
              <?= esc_html($name) ?> — <?= number_format($price, 0, ',', ' ') ?> ₽
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
              <?= esc_html($name) ?> — <?= number_format($price, 0, ',', ' ') ?> ₽
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="wrap-tile-area ">
        <label>Площадь плитки (м²):</label>
        <input type="number" id="tile-area" value="0" min="0" max="9999" step="0.1" disabled>
      </div>

      <div class="wrap-uslugi col-1-2">
        <fieldset>
          <legend>Доп. услуги</legend>
          <?php foreach ($data['uslugi'] as $name => $price): ?>
            <label>
              <input type="checkbox" class="usluga" value="<?= esc_attr($price) ?>">
              <?= esc_html($name) ?> — <?= number_format($price, 0, ',', ' ') ?> ₽
            </label><br>
          <?php endforeach; ?>
        </fieldset>
      </div>

      <div id="calc-result" class="col-1-2" style="margin-top:15px; font-weight:bold;">Итого: 0 ₽</div>
      <div id="price-product" class="price-product d-none"><?php global $product; echo $product->get_price(); ?></div>
    </div>

    <script>
      const dataMap = <?= json_encode($data['mapping'], JSON_UNESCAPED_UNICODE) ?>;
      const tumbyAll = <?= json_encode($data['tumby'], JSON_UNESCAPED_UNICODE) ?>;

      document.addEventListener('DOMContentLoaded', () => {
        const stellaSelect = document.getElementById('stella');
        const tumbaSelect = document.getElementById('tumba');
        const plitkaSelect = document.getElementById('plitka');
        const tileAreaInput = document.getElementById('tile-area');
        const resultEl = document.getElementById('calc-result');

        // Функция пересчёта
        const calculate = () => {
          const stellaVal = stellaSelect.value;
          const tumbaPrice = parseFloat(tumbaSelect.value) || 0;
          const tilePrice = parseFloat(plitkaSelect.value) || 0;
          const area = parseFloat(tileAreaInput.value) || 0;

          const stellaPrice = stellaVal ? parseFloat(stellaSelect.selectedOptions[0].dataset.price) || 0 : 0;
          
          let sum = <?php echo $product->get_price() ?>;

          if (stellaPrice > 0) { sum += stellaPrice * 2 }
          console.log(sum)
          if (tumbaPrice > 0) { sum += tumbaPrice * 2 }
          console.log(sum)
          if (tilePrice > 0 && area > 0) { sum += tilePrice * area * 2 }
          console.log(sum)

          // sum += 6000 * 2; // Цветник 
          // sum += 4000 * 2; // Основание 

          // Установка: зависит от высоты стеллы
          if (stellaVal) {
            const height = parseInt(stellaVal.split('×')[0]) || 0;
            document.querySelectorAll('.wrap-uslugi label').forEach( el => {
              let v = { '8k'  : '<input type="checkbox" class="usluga" value="8000"> Установка стелы — 8 000 ₽', 
                        '10k' : '<input type="checkbox" class="usluga" value="10000"> Установка стелы — 10 000 ₽' }
              
              if (el.textContent.includes('Установка стелы')) {
                isChecked = el.querySelector('input').checked;
                if (height <= 1000) { 
                  el.innerHTML = v['8k'];  el.querySelector('input').value = 8000;} 
                else { 
                  el.innerHTML = v['10k']; el.querySelector('input').value = 10000; 
                }
                el.querySelector('input').checked = isChecked;
              }
              console.log(el.querySelector('input'));
              
            });
          }

          // При клике на услуги пересчитывать
          document.querySelectorAll('.usluga').forEach( el => {
            el.addEventListener('click', () => {
              calculate();
            })
          })

          // Доп. услуги
          document.querySelectorAll('.usluga:checked').forEach(el => {
            sum += parseFloat(el.value) || 0;
            console.log(sum);
          });

          resultEl.textContent = `Итого: ${sum.toLocaleString('ru-RU')} ₽`;
        };

        // Обновление тумбы при выборе стеллы
        stellaSelect.addEventListener('change', () => {
          const val = stellaSelect.value;
          tumbaSelect.innerHTML = '<option value="">Выберите тумбу</option>';
          tumbaSelect.disabled = true;

          if (val && dataMap[val]) {
            tumbaSelect.disabled = false;
            dataMap[val].forEach(size => {
              if (tumbyAll[size]) {
                const opt = document.createElement('option');
                opt.value = tumbyAll[size];
                opt.textContent = `${size} — ${parseFloat(tumbyAll[size]).toLocaleString('ru-RU')} ₽`;
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

        // Слушаем все изменения
        tumbaSelect.addEventListener('change', calculate);
        tileAreaInput.addEventListener('input', calculate);
        document.querySelectorAll('.usluga').forEach(cb => {
          cb.addEventListener('change', calculate);
        });

        // Первый расчёт (если что-то выбрано по умолчанию)
        calculate();
      });
    </script>
    <?php
    return ob_get_clean();
}