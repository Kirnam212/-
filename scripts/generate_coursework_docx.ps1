param(
    [string]$InputPath = "docs/coursework.md",
    [string]$OutputPath = "docs/Coursework_CookOverflow.docx"
)

Set-StrictMode -Version Latest
$ErrorActionPreference = "Stop"

function Escape-XmlText {
    param([string]$Text)

    if ($null -eq $Text) {
        return ""
    }

    $Text = $Text.Replace("&", "&amp;")
    $Text = $Text.Replace("<", "&lt;")
    $Text = $Text.Replace(">", "&gt;")

    return $Text
}

function New-RunXml {
    param(
        [string]$Text,
        [int]$Size = 28,
        [switch]$Bold,
        [switch]$Italic
    )

    $escaped = Escape-XmlText $Text
    $boldXml = if ($Bold) { "<w:b/>" } else { "" }
    $italicXml = if ($Italic) { "<w:i/>" } else { "" }

    return "<w:r><w:rPr><w:rFonts w:ascii=`"Times New Roman`" w:hAnsi=`"Times New Roman`" w:cs=`"Times New Roman`"/><w:sz w:val=`"$Size`"/><w:szCs w:val=`"$Size`"/>$boldXml$italicXml</w:rPr><w:t xml:space=`"preserve`">$escaped</w:t></w:r>"
}

function New-ParagraphXml {
    param(
        [string]$Text,
        [string]$Style = "Normal",
        [string]$Justify = "",
        [switch]$KeepNext
    )

    $styleXml = if ($Style) { "<w:pStyle w:val=`"$Style`"/>" } else { "" }
    $jcXml = if ($Justify) { "<w:jc w:val=`"$Justify`"/>" } else { "" }
    $keepNextXml = if ($KeepNext) { "<w:keepNext/>" } else { "" }

    return "<w:p><w:pPr>$styleXml$jcXml$keepNextXml</w:pPr>$(New-RunXml -Text $Text)</w:p>"
}

function New-PageBreakXml {
    return "<w:p><w:r><w:br w:type=`"page`"/></w:r></w:p>"
}

function New-TocFieldXml {
    return @"
<w:p>
  <w:pPr>
    <w:pStyle w:val="NormalNoIndent"/>
  </w:pPr>
  <w:r>
    <w:fldChar w:fldCharType="begin"/>
  </w:r>
  <w:r>
    <w:instrText xml:space="preserve"> TOC \o "1-3" \h \z \u </w:instrText>
  </w:r>
  <w:r>
    <w:fldChar w:fldCharType="separate"/>
  </w:r>
  <w:r>
    <w:rPr>
      <w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman" w:cs="Times New Roman"/>
      <w:sz w:val="28"/>
      <w:szCs w:val="28"/>
    </w:rPr>
    <w:t>Update the table of contents in Word if needed by pressing F9.</w:t>
  </w:r>
  <w:r>
    <w:fldChar w:fldCharType="end"/>
  </w:r>
</w:p>
"@
}

function Convert-MarkdownToOpenXml {
    param([string[]]$Lines)

    $xmlParts = New-Object System.Collections.Generic.List[string]

    foreach ($rawLine in $Lines) {
        $line = $rawLine.TrimEnd()

        if ($line -eq "") {
            continue
        }

        if ($line -eq "<!--PAGEBREAK-->") {
            $xmlParts.Add((New-PageBreakXml))
            continue
        }

        if ($line.StartsWith("# ")) {
            $xmlParts.Add((New-ParagraphXml -Text $line.Substring(2) -Style "Heading1" -KeepNext))
            continue
        }

        if ($line.StartsWith("## ")) {
            $xmlParts.Add((New-ParagraphXml -Text $line.Substring(3) -Style "Heading2" -KeepNext))
            continue
        }

        if ($line.StartsWith("### ")) {
            $xmlParts.Add((New-ParagraphXml -Text $line.Substring(4) -Style "Heading3" -KeepNext))
            continue
        }

        if ($line.StartsWith("- ")) {
            $xmlParts.Add((New-ParagraphXml -Text ("- " + $line.Substring(2)) -Style "ListParagraph"))
            continue
        }

        if ($line -match '^\d+\.\s') {
            $xmlParts.Add((New-ParagraphXml -Text $line -Style "ListParagraph"))
            continue
        }

        $cleanLine = $line.Replace('`', '')
        $xmlParts.Add((New-ParagraphXml -Text $cleanLine -Style "Normal"))
    }

    return ($xmlParts -join "`n")
}

function Get-StylesXml {
    return @"
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:styles xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
  <w:docDefaults>
    <w:rPrDefault>
      <w:rPr>
        <w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman" w:cs="Times New Roman"/>
        <w:sz w:val="28"/>
        <w:szCs w:val="28"/>
      </w:rPr>
    </w:rPrDefault>
    <w:pPrDefault>
      <w:pPr>
        <w:spacing w:before="0" w:after="0" w:line="360" w:lineRule="auto"/>
        <w:ind w:firstLine="708"/>
        <w:jc w:val="both"/>
      </w:pPr>
    </w:pPrDefault>
  </w:docDefaults>
  <w:style w:type="paragraph" w:default="1" w:styleId="Normal">
    <w:name w:val="Normal"/>
    <w:qFormat/>
  </w:style>
  <w:style w:type="paragraph" w:styleId="NormalNoIndent">
    <w:name w:val="Normal No Indent"/>
    <w:basedOn w:val="Normal"/>
    <w:pPr>
      <w:spacing w:before="0" w:after="0" w:line="360" w:lineRule="auto"/>
      <w:jc w:val="both"/>
    </w:pPr>
  </w:style>
  <w:style w:type="paragraph" w:styleId="Heading1">
    <w:name w:val="heading 1"/>
    <w:basedOn w:val="Normal"/>
    <w:qFormat/>
    <w:pPr>
      <w:keepNext/>
      <w:spacing w:before="240" w:after="120" w:line="360" w:lineRule="auto"/>
      <w:ind w:firstLine="0"/>
      <w:jc w:val="center"/>
      <w:outlineLvl w:val="0"/>
    </w:pPr>
    <w:rPr>
      <w:b/>
      <w:sz w:val="32"/>
      <w:szCs w:val="32"/>
    </w:rPr>
  </w:style>
  <w:style w:type="paragraph" w:styleId="Heading2">
    <w:name w:val="heading 2"/>
    <w:basedOn w:val="Normal"/>
    <w:qFormat/>
    <w:pPr>
      <w:keepNext/>
      <w:spacing w:before="200" w:after="100" w:line="360" w:lineRule="auto"/>
      <w:ind w:firstLine="0"/>
      <w:jc w:val="left"/>
      <w:outlineLvl w:val="1"/>
    </w:pPr>
    <w:rPr>
      <w:b/>
    </w:rPr>
  </w:style>
  <w:style w:type="paragraph" w:styleId="Heading3">
    <w:name w:val="heading 3"/>
    <w:basedOn w:val="Normal"/>
    <w:qFormat/>
    <w:pPr>
      <w:keepNext/>
      <w:spacing w:before="160" w:after="80" w:line="360" w:lineRule="auto"/>
      <w:ind w:firstLine="0"/>
      <w:jc w:val="left"/>
      <w:outlineLvl w:val="2"/>
    </w:pPr>
    <w:rPr>
      <w:b/>
      <w:i/>
    </w:rPr>
  </w:style>
  <w:style w:type="paragraph" w:styleId="ListParagraph">
    <w:name w:val="List Paragraph"/>
    <w:basedOn w:val="Normal"/>
    <w:pPr>
      <w:spacing w:before="0" w:after="0" w:line="360" w:lineRule="auto"/>
      <w:ind w:left="708" w:firstLine="0"/>
      <w:jc w:val="both"/>
    </w:pPr>
  </w:style>
  <w:style w:type="paragraph" w:styleId="TitleCenter">
    <w:name w:val="Title Center"/>
    <w:basedOn w:val="NormalNoIndent"/>
    <w:pPr>
      <w:spacing w:before="0" w:after="120" w:line="360" w:lineRule="auto"/>
      <w:jc w:val="center"/>
    </w:pPr>
  </w:style>
  <w:style w:type="paragraph" w:styleId="TitleMain">
    <w:name w:val="Title Main"/>
    <w:basedOn w:val="TitleCenter"/>
    <w:pPr>
      <w:spacing w:before="120" w:after="120" w:line="360" w:lineRule="auto"/>
      <w:jc w:val="center"/>
    </w:pPr>
    <w:rPr>
      <w:b/>
      <w:sz w:val="36"/>
      <w:szCs w:val="36"/>
    </w:rPr>
  </w:style>
</w:styles>
"@
}

function Get-DocumentXml {
    param([string]$BodyContent)

    return @"
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:wpc="http://schemas.microsoft.com/office/word/2010/wordprocessingCanvas"
    xmlns:mc="http://schemas.openxmlformats.org/markup-compatibility/2006"
    xmlns:o="urn:schemas-microsoft-com:office:office"
    xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"
    xmlns:m="http://schemas.openxmlformats.org/officeDocument/2006/math"
    xmlns:v="urn:schemas-microsoft-com:vml"
    xmlns:wp14="http://schemas.microsoft.com/office/word/2010/wordprocessingDrawing"
    xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing"
    xmlns:w10="urn:schemas-microsoft-com:office:word"
    xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"
    xmlns:w14="http://schemas.microsoft.com/office/word/2010/wordml"
    xmlns:w15="http://schemas.microsoft.com/office/word/2012/wordml"
    xmlns:wpg="http://schemas.microsoft.com/office/word/2010/wordprocessingGroup"
    xmlns:wpi="http://schemas.microsoft.com/office/word/2010/wordprocessingInk"
    xmlns:wne="http://schemas.microsoft.com/office/word/2006/wordml"
    xmlns:wps="http://schemas.microsoft.com/office/word/2010/wordprocessingShape"
    mc:Ignorable="w14 w15 wp14">
  <w:body>
    $BodyContent
    <w:sectPr>
      <w:footerReference w:type="default" r:id="rId3"/>
      <w:titlePg/>
      <w:pgSz w:w="11906" w:h="16838"/>
      <w:pgMar w:top="1134" w:right="567" w:bottom="1134" w:left="1134" w:header="708" w:footer="708" w:gutter="0"/>
      <w:pgNumType w:start="1"/>
      <w:docGrid w:linePitch="360"/>
    </w:sectPr>
  </w:body>
</w:document>
"@
}

function Get-FooterXml {
    return @"
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:ftr xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
  <w:p>
    <w:pPr>
      <w:jc w:val="center"/>
    </w:pPr>
    <w:r>
      <w:rPr>
        <w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman" w:cs="Times New Roman"/>
        <w:sz w:val="24"/>
        <w:szCs w:val="24"/>
      </w:rPr>
      <w:fldChar w:fldCharType="begin"/>
    </w:r>
    <w:r>
      <w:rPr>
        <w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman" w:cs="Times New Roman"/>
        <w:sz w:val="24"/>
        <w:szCs w:val="24"/>
      </w:rPr>
      <w:instrText xml:space="preserve"> PAGE </w:instrText>
    </w:r>
    <w:r>
      <w:rPr>
        <w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman" w:cs="Times New Roman"/>
        <w:sz w:val="24"/>
        <w:szCs w:val="24"/>
      </w:rPr>
      <w:fldChar w:fldCharType="separate"/>
    </w:r>
    <w:r>
      <w:rPr>
        <w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman" w:cs="Times New Roman"/>
        <w:sz w:val="24"/>
        <w:szCs w:val="24"/>
      </w:rPr>
      <w:t>2</w:t>
    </w:r>
    <w:r>
      <w:rPr>
        <w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman" w:cs="Times New Roman"/>
        <w:sz w:val="24"/>
        <w:szCs w:val="24"/>
      </w:rPr>
      <w:fldChar w:fldCharType="end"/>
    </w:r>
  </w:p>
</w:ftr>
"@
}

function Get-SettingsXml {
    return @"
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:settings xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
  <w:updateFields w:val="true"/>
</w:settings>
"@
}

function Get-ContentTypesXml {
    return @"
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
  <Override PartName="/word/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml"/>
  <Override PartName="/word/settings.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.settings+xml"/>
  <Override PartName="/word/footer1.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.footer+xml"/>
  <Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>
  <Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>
</Types>
"@
}

function Get-PackageRelsXml {
    return @"
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>
  <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>
</Relationships>
"@
}

function Get-DocumentRelsXml {
    return @"
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/settings" Target="settings.xml"/>
  <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/footer" Target="footer1.xml"/>
</Relationships>
"@
}

function Get-CoreXml {
    $now = (Get-Date).ToUniversalTime().ToString("s") + "Z"

    return @"
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:dcterms="http://purl.org/dc/terms/"
    xmlns:dcmitype="http://purl.org/dc/dcmitype/"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <dc:title>Coursework for CookOverflow web application</dc:title>
  <dc:subject>Cooking knowledge exchange web application</dc:subject>
  <dc:creator>Codex</dc:creator>
  <cp:lastModifiedBy>Codex</cp:lastModifiedBy>
  <dcterms:created xsi:type="dcterms:W3CDTF">$now</dcterms:created>
  <dcterms:modified xsi:type="dcterms:W3CDTF">$now</dcterms:modified>
</cp:coreProperties>
"@
}

function Get-AppXml {
    return @"
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties"
    xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">
  <Application>Microsoft Office Word</Application>
  <DocSecurity>0</DocSecurity>
  <ScaleCrop>false</ScaleCrop>
  <HeadingPairs>
    <vt:vector size="2" baseType="variant">
      <vt:variant>
        <vt:lpstr>Sections</vt:lpstr>
      </vt:variant>
      <vt:variant>
        <vt:i4>1</vt:i4>
      </vt:variant>
    </vt:vector>
  </HeadingPairs>
  <TitlesOfParts>
    <vt:vector size="1" baseType="lpstr">
      <vt:lpstr>Coursework</vt:lpstr>
    </vt:vector>
  </TitlesOfParts>
  <Company></Company>
  <LinksUpToDate>false</LinksUpToDate>
  <SharedDoc>false</SharedDoc>
  <HyperlinksChanged>false</HyperlinksChanged>
  <AppVersion>16.0000</AppVersion>
</Properties>
"@
}

if (-not (Test-Path $InputPath)) {
    throw "Coursework source file was not found: $InputPath"
}

$lines = Get-Content -Path $InputPath -Encoding UTF8
$bodyXml = Convert-MarkdownToOpenXml -Lines $lines

$titlePageParts = @(
    (New-ParagraphXml -Text "Educational organization" -Style "TitleCenter" -Justify "center"),
    (New-ParagraphXml -Text "Specialty 09.02.07 Information Systems and Programming" -Style "TitleCenter" -Justify "center"),
    (New-ParagraphXml -Text "PM.09 Design, development and optimization of web applications" -Style "TitleCenter" -Justify "center"),
    (New-ParagraphXml -Text "" -Style "TitleCenter" -Justify "center"),
    (New-ParagraphXml -Text "COURSEWORK" -Style "TitleMain" -Justify "center"),
    (New-ParagraphXml -Text "Topic:" -Style "TitleCenter" -Justify "center"),
    (New-ParagraphXml -Text "Development of a web application for exchanging culinary knowledge" -Style "TitleCenter" -Justify "center"),
    (New-ParagraphXml -Text "" -Style "TitleCenter" -Justify "center"),
    (New-ParagraphXml -Text "Student: ______________________________" -Style "TitleCenter" -Justify "center"),
    (New-ParagraphXml -Text "Group: _______________________________" -Style "TitleCenter" -Justify "center"),
    (New-ParagraphXml -Text "Supervisor: _________________________" -Style "TitleCenter" -Justify "center"),
    (New-ParagraphXml -Text "" -Style "TitleCenter" -Justify "center"),
    (New-ParagraphXml -Text "2026" -Style "TitleCenter" -Justify "center"),
    (New-PageBreakXml),
    (New-ParagraphXml -Text "CONTENTS" -Style "Heading1" -Justify "center"),
    (New-TocFieldXml),
    (New-PageBreakXml)
)

$documentXml = Get-DocumentXml -BodyContent (($titlePageParts -join "`n") + "`n" + $bodyXml)

$packageRoot = Join-Path ([System.IO.Path]::GetTempPath()) ("cookoverflow_docx_" + [System.Guid]::NewGuid().ToString("N"))
New-Item -ItemType Directory -Path $packageRoot | Out-Null
New-Item -ItemType Directory -Path (Join-Path $packageRoot "_rels") | Out-Null
New-Item -ItemType Directory -Path (Join-Path $packageRoot "docProps") | Out-Null
New-Item -ItemType Directory -Path (Join-Path $packageRoot "word") | Out-Null
New-Item -ItemType Directory -Path (Join-Path $packageRoot "word\_rels") | Out-Null

Set-Content -LiteralPath (Join-Path $packageRoot "[Content_Types].xml") -Value (Get-ContentTypesXml) -Encoding UTF8
Set-Content -LiteralPath (Join-Path $packageRoot "_rels\.rels") -Value (Get-PackageRelsXml) -Encoding UTF8
Set-Content -LiteralPath (Join-Path $packageRoot "docProps\core.xml") -Value (Get-CoreXml) -Encoding UTF8
Set-Content -LiteralPath (Join-Path $packageRoot "docProps\app.xml") -Value (Get-AppXml) -Encoding UTF8
Set-Content -LiteralPath (Join-Path $packageRoot "word\document.xml") -Value $documentXml -Encoding UTF8
Set-Content -LiteralPath (Join-Path $packageRoot "word\styles.xml") -Value (Get-StylesXml) -Encoding UTF8
Set-Content -LiteralPath (Join-Path $packageRoot "word\settings.xml") -Value (Get-SettingsXml) -Encoding UTF8
Set-Content -LiteralPath (Join-Path $packageRoot "word\footer1.xml") -Value (Get-FooterXml) -Encoding UTF8
Set-Content -LiteralPath (Join-Path $packageRoot "word\_rels\document.xml.rels") -Value (Get-DocumentRelsXml) -Encoding UTF8

$outputDirectory = Split-Path -Parent $OutputPath
if ($outputDirectory -and -not (Test-Path $outputDirectory)) {
    New-Item -ItemType Directory -Path $outputDirectory | Out-Null
}

$resolvedOutput = if ([System.IO.Path]::IsPathRooted($OutputPath)) {
    $OutputPath
} else {
    Join-Path (Get-Location) $OutputPath
}

if (Test-Path $resolvedOutput) {
    Remove-Item $resolvedOutput -Force
}

Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem
[System.IO.Compression.ZipFile]::CreateFromDirectory($packageRoot, $resolvedOutput)

Remove-Item $packageRoot -Recurse -Force

Write-Output "DOCX_CREATED: $resolvedOutput"
