# Agent Skill: Hyperthymesia (Context Persistence & Memory Compaction)

## 1. Role & Core Objective
You are equipped with **Hyperthymesia**—a specialized protocol for managing long-term cognitive continuity. Your primary objective is to prevent "context rot" or information loss due to token limit exhaustion. You must proactively monitor the conversation depth and synthesize "Memory Snapshots" before context is cleared.

---

## 2. Context Threshold Monitoring
*   **Trigger Condition:** When the conversation length exceeds 80% of your maximum token limit or when a major project milestone is reached.
*   **Heuristic Check:** If you find yourself forgetting earlier technical constraints or if the user asks for a "Summary of Progress," initiate the **Compaction Protocol**.

---

## 3. The Compaction Protocol (Summarization)
When triggering a memory snapshot, you must synthesize the current state into a high-density Markdown document. This document must contain:

1.  **Project Identity:** Current project name, stack (e.g., Laravel, TALL Stack), and primary goals.
2.  **Architectural Decisions:** Any technical "laws" established (e.g., "Use class-based Blade components only," "Strict Tailwind design tokens").
3.  **Active Workstreams:** Current tasks in progress and their specific line-item status.
4.  **The "Knowledge Debt":** Questions yet to be answered or bugs yet to be resolved.
5.  **Critical Snippets:** Ultra-compact versions of essential code logic that must persist.

---

## 4. Persistence & Export Instructions
Upon generating a session summary, you must format the output as a standalone file instruction for the user:

*   **File Path:** `agent-session/`
*   **Filename Format:** `session-YYYYMMDD-HHMM.md` (Use the current UTC or local datetime).
*   **Standard Header:**
    ```markdown
    # Session Memory Snapshot | Date: [Current Date]
    ## Context ID: [Generate unique Hash]
    ---
