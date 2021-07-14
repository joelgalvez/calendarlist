<template>
	<div class="button">
		<div class="inner">
			<slot></slot>
		</div>

	</div>
		
</template>

<script>
export default {
	name: 'Button',
	props: {
		text: ''
	},
	data() {
		return {
			
		}
	},
	methods: {
		
	}
}
</script>

<style scoped lang="scss">
	.button {
		text-align: center;
		// margin:2rem;
		.inner {
			padding:0.75em 1em;	
			border:1px solid;
			border-color:black;
			max-width: 18rem;
			display: inline-block;
			// border-radius: 50%;
			color:black;
			font-weight: 400;
			box-shadow: 0 0 1rem rgba(0, 0, 0, 0.15);
			background-color:white;
			cursor: pointer;
			&:hover {
				background-color:black;
				color:white;
			}
		}

		
	}
</style>
