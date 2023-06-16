<?php
error_reporting(0);
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    function parseRequestHeader($headerString, $format = "javascript") {
      $headers = explode("\n", $headerString);
      $headerArray = [];
      foreach ($headers as $header) {
        $header = trim($header);
        if (!empty($header) && !preg_match('/\b(?:POST|GET)\b/i', $header)) {
          $separatorIndex = strpos($header, ":");
          $key = trim(substr($header, 0, $separatorIndex));
          $value = trim(substr($header, $separatorIndex + 1));
          $headerArray[] = ["key" => $key, "value" => $value];
        }
      }
  
      if ($format === "python") {
        $pythonFormattedHeader = "{\n";
        foreach ($headerArray as $header) {
          $pythonFormattedHeader .= sprintf("    '%s': '%s',\n", $header["key"], $header["value"]);
        }
        $pythonFormattedHeader .= "}";
        return $pythonFormattedHeader;
      } elseif ($format === "python-json") {
        $pythonJsonFormattedHeader = "{\n";
        foreach ($headerArray as $header) {
          $pythonJsonFormattedHeader .= sprintf("    \"%s\": \"%s\",\n", $header["key"], $header["value"]);
        }
        $pythonJsonFormattedHeader .= "}";
        return $pythonJsonFormattedHeader;
      } elseif ($format === "php") {
        $phpFormattedHeader = "array(\n";
        foreach ($headerArray as $header) {
          $phpFormattedHeader .= sprintf("    \"%s\" => \"%s\",\n", $header["key"], $header["value"]);
        }
        $phpFormattedHeader .= ")";
        return $phpFormattedHeader;
      } elseif ($format === "php-json") {
        $phpJsonFormattedHeader = "[\n";
        foreach ($headerArray as $header) {
          $phpJsonFormattedHeader .= sprintf("    \"%s\" => \"%s\",\n", $header["key"], $header["value"]);
        }
        $phpJsonFormattedHeader .= "]";
        return $phpJsonFormattedHeader;
      } else {
        return $headerArray;
      }
    }
  
    $headerInput = $_POST["headerInput"] ?? "";
    $formatSelect = $_POST["formatSelect"] ?? "javascript";
    $output = parseRequestHeader($headerInput, $formatSelect);
    $outputJson = $formatSelect === "javascript" ? json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $output;
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Request Header Maker</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Rubik&display=swap');
    body {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-height: 50vh;
      padding: 20px;
      font-family: 'Rubik', sans-serif;
    }

    .button-3 {
        appearance: none;
        background-color: #2ea44f;
        border: 1px solid rgba(27, 31, 35, .15);
        border-radius: 6px;
        box-shadow: rgba(27, 31, 35, .1) 0 1px 0;
        box-sizing: border-box;
        color: #fff;
        cursor: pointer;
        display: inline-block;
        font-family: -apple-system,system-ui,"Segoe UI",Helvetica,Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji";
        font-size: 14px;
        font-weight: 600;
        line-height: 20px;
        padding: 6px 16px;
        position: relative;
        text-align: center;
        text-decoration: none;
        user-select: none;
        -webkit-user-select: none;
        touch-action: manipulation;
        vertical-align: middle;
        white-space: nowrap;
        }

        .button-3:focus:not(:focus-visible):not(.focus-visible) {
        box-shadow: none;
        outline: none;
        }

        .button-3:hover {
        background-color: #2c974b;
        }

        .button-3:focus {
        box-shadow: rgba(46, 164, 79, .4) 0 0 0 3px;
        outline: none;
        }

        .button-3:disabled {
        background-color: #94d3a2;
        border-color: rgba(27, 31, 35, .1);
        color: rgba(255, 255, 255, .8);
        cursor: default;
        }

        .button-3:active {
        background-color: #298e46;
        box-shadow: rgba(20, 70, 32, .2) 0 1px 0 inset;
        }

    h1 {
      text-align: center;
    }

    /* label {
      text-align: center;
    } */

    .container {
      display: flex;
      flex-direction: column;
      align-items: center;
      margin-bottom: 20px;
      max-width: 100%;
    }

    textarea {
      width: 100%;
      height: 150px;
      resize: vertical;
    }

    .output-container {
      display: none;
      width: auto;
      margin-top: 20px;
      max-width: 100%;
      align-items: center;
    }

    .output-container.show {
      display: block;
    }

    @media (min-width: 480px) {
      .container {
        max-width: 480px;
      }
    }


    select {
    appearance: none;
    border: none;
    font-size: 16px;
    padding: 10px 40px 10px 20px;
    position: relative;
    background-color: #f5f5f5;
    color: #333333;
    cursor: pointer;
    }
    option {
    border: none;
    font-size: 16px;
    padding: 10px 20px;
    background-color: #f5f5f5;
    color: #333333;
    cursor: pointer;
    transform: translateX(-100%);
    transition: transform 0.3s ease-in-out;
    }

    select:after {
    content: "";
    width: 0;
    height: 0;
    border-left: 6px solid transparent;
    border-right: 6px solid transparent;
    border-top: 6px solid #333333;
    position: absolute;
    top: 50%;
    right: 15px;
    transform: translateY(-50%);
    pointer-events: none;
    }

    select:focus {
    outline: none;
    box-shadow: 0 0 3px rgba(81, 203, 238, 1);
    animation: pulse-border 1s ease-in-out infinite;
    }
    select:focus option {
    transform: translateX(0);
    }

    @keyframes pulse-border {
    0% {
        box-shadow: 0 0 0 0 rgba(81, 203, 238, 0.4);
    }
    100% {
        box-shadow: 0 0 0 10px rgba(81, 203, 238, 0);
    }
    }
  </style>
  <script>
    function copyOutput() {
      const outputTextarea = document.getElementById('output');
      outputTextarea.select();
      document.execCommand('copy');
      alert('Output copied to clipboard!');
    }
  </script>
</head>
<body>
  <h1>Request Header Maker</h1>
  <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <div class="container">
      <label for="headerInput">Enter Request Header:</label><br>
      <textarea placeholder="Paste Request Header Here.." id="headerInput" name="headerInput" rows="10" cols="80"><?php echo isset($headerInput) ? htmlspecialchars($headerInput) : ""; ?></textarea>
    </div>
    <div class="container">
      <label for="formatSelect">Select Output Format:</label>
      <br>
      <select id="formatSelect" name="formatSelect">
        <option value="javascript" <?php echo $formatSelect === "javascript" ? "selected" : ""; ?>>JavaScript Object</option>
        <option value="python" <?php echo $formatSelect === "python" ? "selected" : ""; ?>>Python Array</option>
        <option value="python-json" <?php echo $formatSelect === "python-json" ? "selected" : ""; ?>>Python JSON</option>
        <option value="php" <?php echo $formatSelect === "php" ? "selected" : ""; ?>>PHP Array</option>
        <option value="php-json" <?php echo $formatSelect === "php-json" ? "selected" : ""; ?>>PHP JSON</option>
      </select>
    </div>
    <div class="container">
      <button onclick='ready()' class="button-3" type="submit">Create Header</button>
    </div>
  </form>
  
  <?php if (isset($output)): ?>
  <div id="outputContainer" class="output-container show">
    <div class="container">
      <textarea rows="10" cols="80" id="output" readonly><?php echo htmlspecialchars($outputJson); ?></textarea>
    </div>
    <div class="container">
      <button id="copyButton" class="button-3" onclick="copyOutput()">Copy Output</button>
    </div>
  </div>
  <?php endif; ?>
</body>
</html>
