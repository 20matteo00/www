<div class="container p-4">
    <h1 class="text-center"><i class="bi bi-calculator me-2"></i>Calcolatrice</h1>

    <div class="calcolatrice container p-3" style="max-width: 300px; border: 1px solid #ccc; border-radius: 8px;">
        <input type="text" class="form-control mb-2 text-end" id="display" readonly value="0">

        <div class="row g-1 mb-1">
            <div class="col"><button class="btn btn-secondary w-100" onclick="press('7')">7</button></div>
            <div class="col"><button class="btn btn-secondary w-100" onclick="press('8')">8</button></div>
            <div class="col"><button class="btn btn-secondary w-100" onclick="press('9')">9</button></div>
            <div class="col"><button class="btn btn-warning w-100" onclick="press('/')">/</button></div>
        </div>

        <div class="row g-1 mb-1">
            <div class="col"><button class="btn btn-secondary w-100" onclick="press('4')">4</button></div>
            <div class="col"><button class="btn btn-secondary w-100" onclick="press('5')">5</button></div>
            <div class="col"><button class="btn btn-secondary w-100" onclick="press('6')">6</button></div>
            <div class="col"><button class="btn btn-warning w-100" onclick="press('*')">*</button></div>
        </div>

        <div class="row g-1 mb-1">
            <div class="col"><button class="btn btn-secondary w-100" onclick="press('1')">1</button></div>
            <div class="col"><button class="btn btn-secondary w-100" onclick="press('2')">2</button></div>
            <div class="col"><button class="btn btn-secondary w-100" onclick="press('3')">3</button></div>
            <div class="col"><button class="btn btn-warning w-100" onclick="press('-')">-</button></div>
        </div>

        <div class="row g-1 mb-1">
            <div class="col"><button class="btn btn-secondary w-100" onclick="press('0')">0</button></div>
            <div class="col"><button class="btn btn-secondary w-100" onclick="press('.')">.</button></div>
            <div class="col"><button class="btn btn-success w-100" onclick="calculate()">=</button></div>
            <div class="col"><button class="btn btn-warning w-100" onclick="press('+')">+</button></div>
        </div>

        <div class="row g-1">
            <div class="col"><button class="btn btn-danger w-100" onclick="clearDisplay()">C</button></div>
        </div>
    </div>

    <script>
        let expression = '';

        function press(key) {
            if (expression === '0') expression = '';
            expression += key;
            document.getElementById('display').value = expression;
        }

        function calculate() {
            try {
                expression = eval(expression).toString();
                document.getElementById('display').value = expression;
            } catch {
                document.getElementById('display').value = 'Errore';
                expression = '';
            }
        }

        function clearDisplay() {
            expression = '';
            document.getElementById('display').value = '0';
        }
    </script>

</div>