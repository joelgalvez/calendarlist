<template>
	<div class="modal" @click="click">
		<div class="inner">
			<div class="cross" @click="close">
				<svg class="svg" viewBox="0 0 100 100" preserveAspectRatio="none">
					<line vector-effect="non-scaling-stroke" x1="0" y1="0" x2="100" y2="100" stroke="black" stroke-width="1.5" />
					<line vector-effect="non-scaling-stroke" x1="100" y1="0" x2="0" y2="100" stroke="black" stroke-width="1.5" />
				</svg>
			</div>
			<slot></slot>
		</div>
	</div>
</template>

<script>
export default {
	name: 'Modal',
	props: {
		text: ''
	},
	data() {
		return {
			
		}
	},
	methods: {
		click(e) {
			if(e.target === this.$el) {
				this.close();
			}
		},
		close() {
			if(this.$listeners && this.$listeners.close) {
				this.$emit('close');
			}
		}
	}
}
</script>

<style  lang="scss">
	.modal {
		position: absolute;
		z-index: 2;
		top:0;
		left:0;
		width:100%;
		min-height: 100vh;
		display: flex;
		align-items: center;
		justify-content: center;
		background-color: rgba(36, 36, 36, 0.644);

		.inner {
			position: relative;
			.cross {
				cursor: pointer;
				position:absolute;
				top:0;
				right:0;
				width:2rem;
				height:2rem;
				padding:0.5rem;
				svg {
					width:100%;
					height:100%;
				}
			}
			margin:0.5rem;
			max-width:37rem;
			max-height:calc(100vh - 2rem);
			overflow-y:auto;
			overflow-x:hidden;
			background-color: white;
			// min-height: 25rem;
			padding:1.5rem;
			padding-top:2.5rem;
			box-shadow: 0 0 7rem rgba(0, 0, 0, 0.15);
			h1,h2,h3,h4,h5,h6 {
				margin:0.25em 0 0.5em 0;
			}
			h2 {
				font-size: 1.5rem;
			}
			h3 {
				font-size: 0.8rem;
				// line-height: 1.3;
				margin-top:2rem;
			}
			h4 {
				font-size: 0.75rem;
				color:rgb(95, 95, 95);
			}
			hr {
				border:none;
				border-bottom: 2px solid;
				margin:1.5rem auto;
				max-width: 10rem;
			}
		}
	}
</style>
