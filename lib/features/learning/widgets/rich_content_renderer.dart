import 'package:flutter/material.dart';
import '../../../core/constants/app_colors.dart';

// ─────────────────────────────────────────────────────────────────────────────
// RichContentRenderer
// No external dependencies. Parses HTML / Markdown subset into typed nodes,
// then renders each node with purpose-built Flutter widgets.
//
// Supported block types:
//   h1–h6, paragraph, bullet list, numbered list, blockquote,
//   code block, horizontal rule, table
//
// Supported inline styles (inside any text node):
//   **bold**, *italic*, `code`, ~~strikethrough~~
//   <b>, <strong>, <em>, <i>, <code>, <del>, <a href>
// ─────────────────────────────────────────────────────────────────────────────

// ── Public API ────────────────────────────────────────────────────────────────

class RichContentRenderer extends StatelessWidget {
  final String content;
  final double baseFontSize;

  const RichContentRenderer({
    super.key,
    required this.content,
    this.baseFontSize = 15.0,
  });

  @override
  Widget build(BuildContext context) {
    final nodes = ContentParser.parse(content);
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: nodes
          .map((n) => _NodeRenderer(node: n, baseFontSize: baseFontSize))
          .toList(),
    );
  }
}

// ── Content node types ────────────────────────────────────────────────────────

enum ContentNodeType {
  h1, h2, h3, h4, h5, h6,
  paragraph,
  bulletList,
  numberedList,
  blockquote,
  codeBlock,
  hr,
  table,
}

class ContentNode {
  final ContentNodeType type;
  final String text;               // raw text for most nodes
  final List<String> items;        // for list / table rows
  final List<List<String>> rows;   // for table
  final List<String> headers;      // for table

  const ContentNode({
    required this.type,
    this.text = '',
    this.items = const [],
    this.rows = const [],
    this.headers = const [],
  });
}

// ── Parser ────────────────────────────────────────────────────────────────────

class ContentParser {
  static List<ContentNode> parse(String raw) {
    // 1. Normalise line endings
    var src = raw
        .replaceAll('\r\n', '\n')
        .replaceAll('\r', '\n');

    // 2. Convert common HTML block tags to Markdown-like markers
    src = _htmlToMarkers(src);

    // 3. Split into lines
    final lines = src.split('\n');
    final nodes = <ContentNode>[];
    int i = 0;

    while (i < lines.length) {
      final line = lines[i].trimRight();
      final trimmed = line.trim();

      // ── Blank line ─────────────────────────────────────────────────────────
      if (trimmed.isEmpty) { i++; continue; }

      // ── Horizontal rule ────────────────────────────────────────────────────
      if (RegExp(r'^[-*_]{3,}$').hasMatch(trimmed)) {
        nodes.add(const ContentNode(type: ContentNodeType.hr));
        i++; continue;
      }

      // ── ATX Headings (# … ######) ──────────────────────────────────────────
      final hMatch = RegExp(r'^(#{1,6})\s+(.+)$').firstMatch(trimmed);
      if (hMatch != null) {
        final level = hMatch.group(1)!.length;
        final text = hMatch.group(2)!;
        nodes.add(ContentNode(
          type: ContentNodeType.values[level - 1], // h1 = index 0 … h6 = index 5
          text: text,
        ));
        i++; continue;
      }

      // ── Blockquote ─────────────────────────────────────────────────────────
      if (trimmed.startsWith('> ') || trimmed.startsWith('>')) {
        final buf = <String>[];
        while (i < lines.length &&
            (lines[i].trim().startsWith('> ') ||
             lines[i].trim().startsWith('>'))) {
          buf.add(lines[i].trim().replaceFirst(RegExp(r'^>\s?'), ''));
          i++;
        }
        nodes.add(ContentNode(
          type: ContentNodeType.blockquote,
          text: buf.join('\n'),
        ));
        continue;
      }

      // ── Code block (``` … ```) ──────────────────────────────────────────────
      if (trimmed.startsWith('```')) {
        final buf = <String>[];
        i++; // skip opening fence
        while (i < lines.length && !lines[i].trim().startsWith('```')) {
          buf.add(lines[i]);
          i++;
        }
        i++; // skip closing fence
        nodes.add(ContentNode(
          type: ContentNodeType.codeBlock,
          text: buf.join('\n'),
        ));
        continue;
      }

      // ── Bullet list (-, *, +) ───────────────────────────────────────────────
      if (RegExp(r'^[-*+]\s').hasMatch(trimmed)) {
        final items = <String>[];
        while (i < lines.length &&
            RegExp(r'^[-*+]\s').hasMatch(lines[i].trim())) {
          items.add(lines[i].trim().replaceFirst(RegExp(r'^[-*+]\s'), ''));
          i++;
        }
        nodes.add(ContentNode(type: ContentNodeType.bulletList, items: items));
        continue;
      }

      // ── Numbered list (1. 2. …) ────────────────────────────────────────────
      if (RegExp(r'^\d+\.\s').hasMatch(trimmed)) {
        final items = <String>[];
        while (i < lines.length &&
            RegExp(r'^\d+\.\s').hasMatch(lines[i].trim())) {
          items.add(lines[i].trim().replaceFirst(RegExp(r'^\d+\.\s'), ''));
          i++;
        }
        nodes.add(ContentNode(type: ContentNodeType.numberedList, items: items));
        continue;
      }

      // ── Table (pipe-separated) ──────────────────────────────────────────────
      if (trimmed.startsWith('|') && trimmed.endsWith('|')) {
        final tableLines = <String>[];
        while (i < lines.length &&
            lines[i].trim().startsWith('|')) {
          tableLines.add(lines[i].trim());
          i++;
        }
        if (tableLines.length >= 2) {
          final headers = _splitTableRow(tableLines[0]);
          // Row index 1 is the separator (---|---), skip it
          final rows = tableLines
              .skip(2)
              .map(_splitTableRow)
              .toList();
          nodes.add(ContentNode(
            type: ContentNodeType.table,
            headers: headers,
            rows: rows,
          ));
        }
        continue;
      }

      // ── Paragraph ──────────────────────────────────────────────────────────
      // Collect consecutive non-empty, non-special lines
      final buf = <String>[];
      while (i < lines.length) {
        final l = lines[i].trim();
        if (l.isEmpty) break;
        if (RegExp(r'^(#{1,6})\s').hasMatch(l)) break;
        if (l.startsWith('> ')) break;
        if (l.startsWith('```')) break;
        if (RegExp(r'^[-*+]\s').hasMatch(l)) break;
        if (RegExp(r'^\d+\.\s').hasMatch(l)) break;
        if (l.startsWith('|') && l.endsWith('|')) break;
        if (RegExp(r'^[-*_]{3,}$').hasMatch(l)) break;
        buf.add(lines[i].trimRight());
        i++;
      }
      if (buf.isNotEmpty) {
        nodes.add(ContentNode(
          type: ContentNodeType.paragraph,
          text: buf.join(' '),
        ));
      }
    }

    return nodes;
  }

  // ── Convert HTML block tags to text markers ──────────────────────────────
  static String _htmlToMarkers(String html) {
    var s = html;
    // Headings
    for (int lvl = 1; lvl <= 6; lvl++) {
      s = s.replaceAllMapped(
        RegExp('<h$lvl[^>]*>(.*?)</h$lvl>', dotAll: true, caseSensitive: false),
        (m) => '\n${'#' * lvl} ${_stripInlineHtml(m.group(1) ?? '')}\n',
      );
    }
    // Block elements → newlines
    s = s
        .replaceAllMapped(RegExp(r'<p[^>]*>(.*?)</p>', dotAll: true, caseSensitive: false),
            (m) => '\n${m.group(1)}\n')
        .replaceAllMapped(RegExp(r'<blockquote[^>]*>(.*?)</blockquote>', dotAll: true, caseSensitive: false),
            (m) => '${m.group(1)!.trim().split('\n').map((l) => '> $l').join('\n')}\n')
        .replaceAllMapped(RegExp(r'<pre[^>]*>\s*<code[^>]*>(.*?)</code>\s*</pre>', dotAll: true, caseSensitive: false),
            (m) => '\n```\n${m.group(1)}\n```\n')
        .replaceAllMapped(RegExp(r'<code[^>]*>(.*?)</code>', dotAll: true, caseSensitive: false),
            (m) => '`${m.group(1)}`')
        .replaceAllMapped(RegExp(r'<ul[^>]*>(.*?)</ul>', dotAll: true, caseSensitive: false),
            (m) => _convertListItems(m.group(1) ?? '', bullet: true))
        .replaceAllMapped(RegExp(r'<ol[^>]*>(.*?)</ol>', dotAll: true, caseSensitive: false),
            (m) => _convertListItems(m.group(1) ?? '', bullet: false))
        .replaceAllMapped(RegExp(r'<table[^>]*>(.*?)</table>', dotAll: true, caseSensitive: false),
            (m) => _convertTable(m.group(1) ?? ''))
        .replaceAll(RegExp(r'<hr[^>]*/?>',  caseSensitive: false), '\n---\n')
        .replaceAll(RegExp(r'<br[^>]*/?>',  caseSensitive: false), '\n');

    // Inline bold/italic to Markdown equivalents
    s = s
        .replaceAllMapped(RegExp(r'<(strong|b)[^>]*>(.*?)</(strong|b)>', dotAll: true, caseSensitive: false),
            (m) => '**${m.group(2)}**')
        .replaceAllMapped(RegExp(r'<(em|i)[^>]*>(.*?)</(em|i)>', dotAll: true, caseSensitive: false),
            (m) => '*${m.group(2)}*')
        .replaceAllMapped(RegExp(r'<del[^>]*>(.*?)</del>', dotAll: true, caseSensitive: false),
            (m) => '~~${m.group(1)}~~')
        .replaceAllMapped(RegExp(r'<a[^>]*href="([^"]*)"[^>]*>(.*?)</a>', dotAll: true, caseSensitive: false),
            (m) => '${m.group(2)} (${m.group(1)})')
        // Strip any remaining tags
        .replaceAll(RegExp(r'<[^>]+>'), '')
        // HTML entities
        .replaceAll('&nbsp;', ' ')
        .replaceAll('&amp;', '&')
        .replaceAll('&lt;', '<')
        .replaceAll('&gt;', '>')
        .replaceAll('&quot;', '"')
        .replaceAll('&#39;', "'");
    return s;
  }

  static String _stripInlineHtml(String s) =>
      s.replaceAll(RegExp(r'<[^>]+>'), '').trim();

  static String _convertListItems(String inner, {required bool bullet}) {
    final items = RegExp(r'<li[^>]*>(.*?)</li>', dotAll: true, caseSensitive: false)
        .allMatches(inner)
        .toList();
    final buf = StringBuffer('\n');
    for (int j = 0; j < items.length; j++) {
      final text = _stripInlineHtml(items[j].group(1) ?? '');
      buf.writeln(bullet ? '- $text' : '${j + 1}. $text');
    }
    return buf.toString();
  }

  static String _convertTable(String inner) {
    final rows = RegExp(r'<tr[^>]*>(.*?)</tr>', dotAll: true, caseSensitive: false)
        .allMatches(inner)
        .toList();
    if (rows.isEmpty) return '';
    final buf = StringBuffer('\n');
    for (int r = 0; r < rows.length; r++) {
      final cells = RegExp(r'<(td|th)[^>]*>(.*?)</(td|th)>', dotAll: true, caseSensitive: false)
          .allMatches(rows[r].group(1) ?? '')
          .map((m) => _stripInlineHtml(m.group(2) ?? ''))
          .join(' | ');
      buf.writeln('| $cells |');
      if (r == 0) {
        final cols = RegExp(r'<(td|th)').allMatches(rows[0].group(1) ?? '').length;
        buf.writeln('|${List.filled(cols, '---|').join('')}');
      }
    }
    return buf.toString();
  }

  static List<String> _splitTableRow(String row) {
    return row
        .split('|')
        .map((c) => c.trim())
        .where((c) => c.isNotEmpty)
        .toList();
  }
}

// ── Node renderer ─────────────────────────────────────────────────────────────

class _NodeRenderer extends StatelessWidget {
  final ContentNode node;
  final double baseFontSize;
  const _NodeRenderer({required this.node, required this.baseFontSize});

  @override
  Widget build(BuildContext context) {
    switch (node.type) {
      case ContentNodeType.h1: return _Heading(node.text, 1, baseFontSize);
      case ContentNodeType.h2: return _Heading(node.text, 2, baseFontSize);
      case ContentNodeType.h3: return _Heading(node.text, 3, baseFontSize);
      case ContentNodeType.h4: return _Heading(node.text, 4, baseFontSize);
      case ContentNodeType.h5: return _Heading(node.text, 5, baseFontSize);
      case ContentNodeType.h6: return _Heading(node.text, 6, baseFontSize);
      case ContentNodeType.paragraph:
        return Padding(
          padding: const EdgeInsets.only(bottom: 14),
          child: _InlineText(node.text, baseFontSize: baseFontSize),
        );
      case ContentNodeType.bulletList:
        return _BulletList(items: node.items, baseFontSize: baseFontSize);
      case ContentNodeType.numberedList:
        return _NumberedList(items: node.items, baseFontSize: baseFontSize);
      case ContentNodeType.blockquote:
        return _Blockquote(text: node.text, baseFontSize: baseFontSize);
      case ContentNodeType.codeBlock:
        return _CodeBlock(code: node.text, baseFontSize: baseFontSize);
      case ContentNodeType.hr:
        return const _HorizontalRule();
      case ContentNodeType.table:
        return _TableBlock(
            headers: node.headers,
            rows: node.rows,
            baseFontSize: baseFontSize);
    }
  }
}

// ── Heading ───────────────────────────────────────────────────────────────────
class _Heading extends StatelessWidget {
  final String text;
  final int level;
  final double base;
  const _Heading(this.text, this.level, this.base);

  @override
  Widget build(BuildContext context) {
    final sizes = [base + 9, base + 7, base + 5, base + 3, base + 1, base];
    final weights = [
      FontWeight.w900, FontWeight.w800, FontWeight.w800,
      FontWeight.w700, FontWeight.w700, FontWeight.w600,
    ];
    final topPad = level <= 2 ? 24.0 : 18.0;
    return Padding(
      padding: EdgeInsets.only(top: topPad, bottom: 8),
      child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
        if (level <= 2)
          Row(children: [
            Container(
              width: level == 1 ? 5 : 3,
              height: level == 1 ? 22 : 18,
              margin: const EdgeInsets.only(right: 10),
              decoration: BoxDecoration(
                color: AppColors.primary,
                borderRadius: BorderRadius.circular(3),
              ),
            ),
            Expanded(
              child: Text(
                text,
                style: TextStyle(
                  fontSize: sizes[level - 1],
                  fontWeight: weights[level - 1],
                  color: const Color(0xFF1A2B4A),
                  height: 1.25,
                ),
              ),
            ),
          ])
        else
          Text(
            text,
            style: TextStyle(
              fontSize: sizes[level - 1],
              fontWeight: weights[level - 1],
              color: const Color(0xFF1A2B4A),
              height: 1.3,
            ),
          ),
        if (level <= 2)
          Container(
            height: 2,
            margin: const EdgeInsets.only(top: 6),
            decoration: BoxDecoration(
              gradient: LinearGradient(
                colors: [
                  AppColors.primary.withValues(alpha: 0.25),
                  AppColors.primary.withValues(alpha: 0),
                ],
              ),
            ),
          ),
      ]),
    );
  }
}

// ── Bullet list ───────────────────────────────────────────────────────────────
class _BulletList extends StatelessWidget {
  final List<String> items;
  final double baseFontSize;
  const _BulletList({required this.items, required this.baseFontSize});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 14),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: items.asMap().entries.map((e) {
          return Padding(
            padding: const EdgeInsets.only(bottom: 7),
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Padding(
                  padding: EdgeInsets.only(top: baseFontSize * 0.35, right: 10),
                  child: Container(
                    width: 6,
                    height: 6,
                    decoration: const BoxDecoration(
                      color: AppColors.primary,
                      shape: BoxShape.circle,
                    ),
                  ),
                ),
                Expanded(
                  child: _InlineText(e.value, baseFontSize: baseFontSize),
                ),
              ],
            ),
          );
        }).toList(),
      ),
    );
  }
}

// ── Numbered list ─────────────────────────────────────────────────────────────
class _NumberedList extends StatelessWidget {
  final List<String> items;
  final double baseFontSize;
  const _NumberedList({required this.items, required this.baseFontSize});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 14),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: items.asMap().entries.map((e) {
          return Padding(
            padding: const EdgeInsets.only(bottom: 7),
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                SizedBox(
                  width: 26,
                  child: Text(
                    '${e.key + 1}.',
                    style: TextStyle(
                      fontSize: baseFontSize,
                      fontWeight: FontWeight.w700,
                      color: AppColors.primary,
                    ),
                  ),
                ),
                Expanded(
                  child: _InlineText(e.value, baseFontSize: baseFontSize),
                ),
              ],
            ),
          );
        }).toList(),
      ),
    );
  }
}

// ── Blockquote ────────────────────────────────────────────────────────────────
class _Blockquote extends StatelessWidget {
  final String text;
  final double baseFontSize;
  const _Blockquote({required this.text, required this.baseFontSize});

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 14),
      padding: const EdgeInsets.fromLTRB(14, 12, 14, 12),
      decoration: BoxDecoration(
        color: AppColors.primary.withValues(alpha: 0.05),
        borderRadius: const BorderRadius.only(
          topRight: Radius.circular(10),
          bottomRight: Radius.circular(10),
        ),
        border: const Border(
          left: BorderSide(color: AppColors.primary, width: 4),
        ),
      ),
      child: Text(
        text,
        style: TextStyle(
          fontSize: baseFontSize,
          color: const Color(0xFF3D4A5C),
          fontStyle: FontStyle.italic,
          height: 1.6,
        ),
      ),
    );
  }
}

// ── Code block ────────────────────────────────────────────────────────────────
class _CodeBlock extends StatelessWidget {
  final String code;
  final double baseFontSize;
  const _CodeBlock({required this.code, required this.baseFontSize});

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 14),
      width: double.infinity,
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: const Color(0xFF1A2B4A),
        borderRadius: BorderRadius.circular(10),
      ),
      child: SingleChildScrollView(
        scrollDirection: Axis.horizontal,
        child: Text(
          code,
          style: TextStyle(
            fontFamily: 'monospace',
            fontSize: baseFontSize - 1,
            color: const Color(0xFF90CAF9),
            height: 1.55,
          ),
        ),
      ),
    );
  }
}

// ── Horizontal rule ───────────────────────────────────────────────────────────
class _HorizontalRule extends StatelessWidget {
  const _HorizontalRule();

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.symmetric(vertical: 16),
      height: 1.5,
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [
            AppColors.primary.withValues(alpha: 0),
            AppColors.primary.withValues(alpha: 0.25),
            AppColors.primary.withValues(alpha: 0),
          ],
        ),
      ),
    );
  }
}

// ── Table ─────────────────────────────────────────────────────────────────────
class _TableBlock extends StatelessWidget {
  final List<String> headers;
  final List<List<String>> rows;
  final double baseFontSize;
  const _TableBlock(
      {required this.headers,
      required this.rows,
      required this.baseFontSize});

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 14),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(10),
        border: Border.all(color: const Color(0xFFE5EAF2)),
      ),
      clipBehavior: Clip.hardEdge,
      child: SingleChildScrollView(
        scrollDirection: Axis.horizontal,
        child: Table(
          defaultColumnWidth: const IntrinsicColumnWidth(),
          border: TableBorder(
            horizontalInside:
                BorderSide(color: const Color(0xFFE5EAF2).withValues(alpha: 0.6)),
            verticalInside:
                BorderSide(color: const Color(0xFFE5EAF2).withValues(alpha: 0.6)),
          ),
          children: [
            // Header row
            if (headers.isNotEmpty)
              TableRow(
                decoration: BoxDecoration(
                  color: AppColors.primary.withValues(alpha: 0.08),
                ),
                children: headers.map((h) => _Cell(h, baseFontSize, isHeader: true)).toList(),
              ),
            // Data rows
            ...rows.map(
              (row) => TableRow(
                children: List.generate(
                  headers.isEmpty ? row.length : headers.length,
                  (ci) => _Cell(
                    ci < row.length ? row[ci] : '',
                    baseFontSize,
                    isHeader: false,
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _Cell extends StatelessWidget {
  final String text;
  final double baseFontSize;
  final bool isHeader;
  const _Cell(this.text, this.baseFontSize, {required this.isHeader});

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 9),
      child: Text(
        text,
        style: TextStyle(
          fontSize: baseFontSize - 1,
          fontWeight: isHeader ? FontWeight.w700 : FontWeight.w400,
          color: isHeader
              ? AppColors.primary
              : const Color(0xFF3D4A5C),
        ),
      ),
    );
  }
}

// ── Inline text renderer ──────────────────────────────────────────────────────
// Parses **bold**, *italic*, `code`, ~~strike~~ into a RichText widget.
class _InlineText extends StatelessWidget {
  final String text;
  final double baseFontSize;

  const _InlineText(this.text, {required this.baseFontSize});

  @override
  Widget build(BuildContext context) {
    return RichText(
      text: TextSpan(
        style: TextStyle(
          fontSize: baseFontSize,
          color: const Color(0xFF3D4A5C),
          height: 1.65,
          fontWeight: FontWeight.w400,
        ),
        children: _parseSpans(text, baseFontSize),
      ),
    );
  }

  static List<TextSpan> _parseSpans(String text, double base) {
    final spans = <TextSpan>[];
    // Combined pattern: **bold**, *italic*, `code`, ~~strike~~
    final pattern = RegExp(
      r'\*\*(.+?)\*\*'       // **bold**
      r'|\*(.+?)\*'          // *italic*
      r'|`(.+?)`'            // `code`
      r'|~~(.+?)~~',         // ~~strikethrough~~
    );

    int cursor = 0;
    for (final m in pattern.allMatches(text)) {
      // Plain text before this match
      if (m.start > cursor) {
        spans.add(TextSpan(text: text.substring(cursor, m.start)));
      }
      if (m.group(1) != null) {
        // **bold**
        spans.add(TextSpan(
          text: m.group(1),
          style: const TextStyle(fontWeight: FontWeight.w800),
        ));
      } else if (m.group(2) != null) {
        // *italic*
        spans.add(TextSpan(
          text: m.group(2),
          style: const TextStyle(fontStyle: FontStyle.italic),
        ));
      } else if (m.group(3) != null) {
        // `code`
        spans.add(TextSpan(
          text: m.group(3),
          style: TextStyle(
            fontFamily: 'monospace',
            fontSize: base - 1,
            color: AppColors.primary,
            backgroundColor: AppColors.primary.withValues(alpha: 0.08),
          ),
        ));
      } else if (m.group(4) != null) {
        // ~~strikethrough~~
        spans.add(TextSpan(
          text: m.group(4),
          style: const TextStyle(decoration: TextDecoration.lineThrough),
        ));
      }
      cursor = m.end;
    }
    // Remaining plain text
    if (cursor < text.length) {
      spans.add(TextSpan(text: text.substring(cursor)));
    }
    return spans.isEmpty ? [TextSpan(text: text)] : spans;
  }
}
