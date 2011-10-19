<?php

/**
 * @file
 * Default theme implementation to navigate books. Presented under nodes that
 * are a part of book outlines.
 *
 * Available variables:
 * - $tree: The immediate children of the current node rendered as an
 *   unordered list.
 * - $current_depth: Depth of the current node within the book outline.
 *   Provided for context.
 * - $prev_url: URL to the previous node.
 * - $prev_title: Title of the previous node.
 * - $parent_url: URL to the parent node.
 * - $parent_title: Title of the parent node. Not printed by default. Provided
 *   as an option.
 * - $next_url: URL to the next node.
 * - $next_title: Title of the next node.
 * - $has_links: Flags TRUE whenever the previous, parent or next data has a
 *   value.
 * - $book_id: The book ID of the current outline being viewed. Same as the
 *   node ID containing the entire outline. Provided for context.
 * - $book_url: The book/node URL of the current outline being viewed.
 *   Provided as an option. Not used by default.
 * - $book_title: The book/node title of the current outline being viewed.
 *   Provided as an option. Not used by default.
 *
 * @see template_preprocess_book_navigation()
 */
?>
<?php  if ($has_children): ?>
  <div id="book-navigation-<?php print $node_id; ?>" class="book-navigation">
      <?php if ($page):?>
        <?php foreach($treenodes as $treenode):?>
            <?php print $treenode; ?>
            <?php print $breadcrumb; ?>
        <?php endforeach;?>
      <?php else:?>
        <?php print $read_more; ?>
      <?php endif;?>
  </div>
<?php else:?>
    <?php print $reportlink;?>
<?php endif;?>
